<?php
namespace packages\userpanel\controllers;

use packages\base\{Options, Response, http, InputValidationException, db, View\Error, Session, json, Validator\CellphoneValidator};
use packages\userpanel;
use packages\userpanel\{
	Controller, View, Log, User, date, Authentication, Country, logs, views, Exceptions\UserIsNotActiveException, Events, Register\UserAttribute
};
use packages\userpanel\Register\RegisterField;
use packages\userpanel\Register\RegisterFields;

class Login extends Controller {

	/**
	 * @var bool indicates that these methods does not need authentication and they can handle themselves.
	 */
	protected $authentication = false;

	/**
	 * Save givin user in Authentication class and set handler to session.
	 * It's also save a log of successfull login database.
	 * 
	 * @param User $user
	 * @return void
	 */
	public static function doLogin(User $user): void {
		$prevUser = null;
		if (Authentication::check()) {
			$prevUser = Authentication::getUser();
		}
		Authentication::setUser($user);
		$handler = new Authentication\SessionHandler();
		$handler->setSession();
		$handler->unlock();
		Authentication::setHandler($handler);
		if ($prevUser and $prevUser->id != $user->id) {
			$prevUsers = $handler->getPreviousUsers();
			$key = array_search($user->id, $prevUsers);
			if ($key === false) {
				$handler->addPreviousUser($prevUser);
			} else {
				$len = count($prevUsers);
				for ($i = $key; $i < $len; $i++) {
					$handler->popPreviousUser();
				}
			}
		}

		$log = new Log();
		$log->user = $user->id;
		$log->title = t("logs.login");
		$log->type = logs\Login::class;
		$log->save();
	}

	public static function getRegisterRules(): array {
		$inputs = array(
			'password' => array(
				'type' => 'string',
				'htmlTags' => true,
				'multiLine' => true,
			),
		);
		foreach (RegisterFields::actives(true) as $field) {
			switch ($field) {
				case RegisterField::EMAIL:
					$inputs[$field->value] = array(
						'type' => 'email',
						'optional' => $field->isOptional(),
					);
					break;
				case RegisterField::CELLPHONE:
					$inputs[$field->value] = array(
						'type' => 'cellphone',
						'optional' => $field->isOptional(),
					);
					break;
				case RegisterField::PHONE:
					$inputs[$field->value] = array(
						'type' => 'phone',
						'optional' => $field->isOptional(),
					);
					break;
				case RegisterField::COUNTRY:
					$inputs[$field->value] = array(
						'type' => Country::class,
						'optional' => $field->isOptional(),
					);
					break;
				case RegisterField::ZIP:
					$inputs[$field->value] = array(
						'type' => 'number',
						'optional' => $field->isOptional(),
					);
					break;
				default:
					$inputs[$field->value] = array(
						'type' => 'string',
						'optional' => $field->isOptional(),
					);
					break;
			}
		}
		return $inputs;
	}

	/**
	 * Get remember token from the cookies and find the active user.
	 *
	 * @return User|null
	 */
	public static function checkRememberToken(): ?User {
		$token = http::$request['cookies']['remember'] ?? null;
		if (!$token or !is_string($token)) {
			return null;
		}
		return (new User)
				->where("remember_token", $token)
				->where("status", User::active)
				->getOne();
	}

	protected Authentication\BruteForceThrottle $bruteForceThrottle;

	public function __construct() {
		parent::__construct();

		/** @var false|null|array{'period'?:int,'count'?:int} $throttleOptions */
		$throttleOptions = Options::get('packages.userpanel.login_and_reset_password.bruteforce_throttle');
		$this->bruteForceThrottle = new Authentication\BruteForceThrottle(
			'login-and-reset-password',
			intval($throttleOptions['period'] ?? 3600),
			intval($throttleOptions['total-limit'] ?? 7),
			intval($throttleOptions['session-limit'] ?? 5),
			null,
			null,
			[
				'ignore-ips' => (isset($throttleOptions['ignore-ips']) and is_array($throttleOptions['ignore-ips'])) ?
					$throttleOptions['ignore-ips'] :
					[],
			]
		);
	}

	/**
	 * validate the inputs, find the user and verify the password.
	 * Finally call the doLogin() for setup the session.
	 *
	 * @return User
	 */
	public function login_helper(): User {
		$this->bruteForceThrottle->mustHasChance();

		$inputs = $this->checkinputs(array(
			'credential' => array(
				'type' => ['email', 'cellphone'],
			),
			'password' => array(
				'type' => 'string',
				'htmlTags' => true,
				'multiLine' => true,
			),
			'remember' => array(
				'type' => 'bool',
				'optional' => true,
			),
		));
		$p = new db\parenthesis();
		$p->where("email", $inputs['credential']);
		$p->orwhere("cellphone", $inputs['credential']);
		$user = (new User())
					->where($p)
					->getOne();
		if (!$user) {
			$this->bruteForceThrottle->loseOneChance();
			throw new InputValidationException('credential');
		}
		if (!$user->password_verify($inputs['password'])) {
			$log = new Log();
			$log->title = t("log.wrongLogin");
			$log->type = logs\WrongLogin::class;
			$log->user = $user->id;
			$log->parameters = [
				'user' => $user,
				'wrongpaswd' => $inputs['password']
			];
			$log->save();

			$this->bruteForceThrottle->loseOneChance();
			throw new InputValidationException('password');
		}
		(new Events\BeforeLogin)->trigger();
		if ($user->status == User::active) {
			self::doLogin($user);
			if (isset($inputs['remember']) and $inputs['remember']) {
				http::setcookie('remember', $user->createRememberToken(), date::time() + 31536000);
			}
		} else {
			throw new UserIsNotActiveException($user->status);
		}
		return $user;
	}

	/**
	 * Show login view.
	 * 
	 * @return Response
	 */
	public function loginView(): Response {
		$this->response->setStatus(false);

		$backTo = (isset(HTTP::$data['backTo']) and
			is_string(HTTP::$data['backTo']) and
			HTTP::$data['backTo'] and
			HTTP::is_safe_referer(http::$data['backTo'])
		) ? HTTP::$data['backTo'] : "";

		$view = View::byName(views\Login::class);
		$view->setDataForm($backTo, 'backTo');
		$view->setCountries((new Country)->get());
		$this->response->setView($view);

		if (Authentication::check()) {
			$this->response->Go($backTo ?: userpanel\url());
		} else {
			$this->bruteForceThrottle->mustHasChance(true);

			$handler = Authentication::getHandler();
			if ($handler instanceof Authentication\SessionHandler && $handler->isLock()) {
				$this->response->setStatus(false);
				$this->response->Go(userpanel\url('lock'));
				return $this->response;
			}

			$user = self::checkRememberToken();
			if ($user) {
				self::doLogin($user);
				$this->response->Go($backTo ?: userpanel\url());
			}
		}

		$this->response->setStatus(true);
		return $this->response;
	}

	/**
	 * @throws InputValidationException with empty input field.
	 * @return Response
	 */
	public function login(): Response {
		$view = View::byName(views\Login::class);
		$backTo = (isset(http::$data['backTo']) and 
					is_string(http::$data['backTo']) and 
					http::$data['backTo'] and 
					http::is_safe_referer(http::$data['backTo'])) ? http::$data['backTo'] : "";
		$view->setDataForm($backTo, 'backTo');
		$this->response->setView($view);
		$this->response->setStatus(false);

		try{
			$user = $this->login_helper();
			$this->response->setStatus(true);
			$this->response->Go($backTo ? $backTo : userpanel\url());
		} catch(UserIsNotActiveException $e) {
			$error = "";
			switch ($e->getStatus()) {
				case User::deactive:
					$error = "user_status_is_deactive_in_login";
					break;
				case User::suspend:
					$error = "user_status_is_suspend_in_login";
					break;
			}
			$this->response->setData(array(
				"error" => array(
					array(
						"type" => Error::FATAL,
						"error" => $error,
					),
				),
			));
		} catch(InputValidationException $e) {
			$e->setInput('');
			throw $e;
		}
		return $this->response;
	}

	/**
	 * Reset authentication and redirect user to login page.
	 * 
	 * @return Response
	 */
	public function logout(): Response {
		$handler = Authentication::getHandler();
		Authentication::check();
		Authentication::forget();
		$this->response->setStatus(true);
		if (!$handler) {
			$handler = new Authentication\SessionHandler();
		}
		$user = $handler->popPreviousUser();
		if ($user) {
			Login::doLogin($user);
			$this->response->Go(userpanel\url());
			return $this->response;
		}
		http::removeCookie('remember');
		$this->response->Go(userpanel\url('login'));
		return $this->response;
	}
	
	/**
	 * Save new user based on inputs.
	 * Also save new log.
	 * 
	 * @param array $rules
	 * @return User
	 */
	public function register_helper(array $ruls): User {
		$inputs = $this->checkinputs($ruls);
		$user = new User();
		foreach (['name', 'lastname', 'email', 'city', 'address', 'zip', 'phone', 'cellphone'] as $key) {
			if (isset($inputs[$key])) {
				$user->$key = $inputs[$key];
			}
		}
		if (!$user->email and !$user->cellphone) {
			throw new Error('no_crediential');
		}
		if (isset($inputs['country'])) {
			$user->country = $inputs['country']->id;
		}
		$reqOptions = Options::get('packages.userpanel.register');
		$user->type = $reqOptions['type'];
		$user->status = $reqOptions['status'] ?? User::active;
		$user->password_hash($inputs['password']);
		unset($inputs['password']);
		(new Events\BeforeRegister)->trigger();
		$user->save();

		if ($user->status == User::active) {
			Authentication::setUser($user);
			$handler = new Authentication\SessionHandler();
			$handler->setSession();
			$handler->unlock();
			Authentication::setHandler($handler);
		
			$log = new Log();
			$log->title = t("log.register");
			$log->type = logs\Register::class;
			$log->user = $user->id;
			$log->parameters = [
				'user' => $user,
				'inputs' => $inputs
			];
			$log->save();
		} else {
			throw new UserIsNotActiveException($user->status);
		}

		return $user;
	}

	/**
	 * Show register form
	 * 
	 * @return Response
	 */
	public function signup(): Response {
		/** @var views\Register $view */
		$view = View::byName(views\Register::class);
		$view->setCountries(Country::get());
		$view->setRegisterFields(RegisterFields::actives(true));
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}

	/**
	 * Register new user
	 * 
	 * @return Response
	 */
	public function register(){
		$view = View::byName(views\Register::class);
		$this->response->setView($view);
		$view->setData(Country::get(), 'countries');
		$this->response->setStatus(false);
		$rules = self::getRegisterRules();
		try {
			$user = $this->register_helper($rules);
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url());
		} catch (UserIsNotActiveException $e) {
			$error = "";
			switch ($e->getStatus()) {
				case User::deactive:
					$error = "user_status_is_deactive_in_register";
					break;
				case User::suspend:
					$error = "user_status_is_suspend_in_register";
					break;
			}
			$this->response->setData(array(
				"error" => array(
					array(
						"type" => Error::FATAL,
						"error" => $error,
					),
				),
			));
		}
		return $this->response;
	}
}
