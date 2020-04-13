<?php
namespace packages\userpanel\controllers;

use packages\base\{Options, Response, http, InputValidationException, db, View\Error, Session, json};
use packages\userpanel;
use packages\userpanel\{Controller, View, Log, User, date, Authentication, Country, logs, views, Exceptions\UserIsNotActiveException};

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
		if ($prevUser) {
			$handler->addPreviousUser($prevUser);
		}

		$log = new Log();
		$log->user = $user->id;
		$log->title = t("logs.login");
		$log->type = logs\Login::class;
		$log->save();
	}

	/**
	 * Get remember token from the cookies and find the active user.
	 * 
	 * @return User|null
	 */
	public static function checkRememberToken(): ?User {
		$token = http::$request['cookies']['remember'] ?? null;
		if (!$token or is_string($token)) {
			return null;
		}
		return (new User)
				->where("remember_token", $token)
				->where("status", User::active)
				->getOne();
	}

	/**
	 * validate the inputs, find the user and verify the password.
	 * Finally call the doLogin() for setup the session.
	 * 
	 * @return User
	 */
	public function login_helper(): User {
		$inputs = $this->checkinputs(array(
			'username' => array(
				'type' => ['email', 'cellphone']
			),
			'password' => array(
				'type' => 'string',
				'htmlTags' => true,
				'multiLine' => true,
			),
			'remember' => array(
				'type' => 'bool',
				'optional' => true,
			)
		));
		$p = new db\parenthesis();
		$p->where("email", $inputs['username']);
		$p->orwhere("cellphone", $inputs['username']);
		$user = (new User())
					->where($p)
					->getOne();
		if (!$user) {
			throw new InputValidationException('username');
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
			throw new InputValidationException('password');
		}
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
		$backTo = (isset(http::$data['backTo']) and 
				is_string(http::$data['backTo']) and 
				http::$data['backTo'] and 
				http::is_safe_referer(http::$data['backTo'])) ? http::$data['backTo'] : "";

		if (!Authentication::check()) {
			$handler = Authentication::getHandler();
			if ($handler instanceof Authentication\SessionHandler && $handler->isLock()) {
				$this->response->setStatus(false);
				$this->response->Go(userpanel\url('lock'));
				return $this->response;
			}
			$user = self::checkRememberToken();
			if ($user) {
				self::doLogin($user);
				$this->response->setStatus(true);
				$this->response->Go($backTo ? $backTo : userpanel\url());
				return $this->response;
			}
		}
		$view = View::byName(views\Login::class);
		$view->setDataForm($backTo, 'backTo');
		$this->response->setView($view);
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
		Authentication::check();
		Authentication::forget();
		$this->response->setStatus(true);
		$handler = new Authentication\SessionHandler();
		Authentication::setHandler($handler);
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
		if (isset($inputs['country'])) {
			$user->country = $inputs['country']->id;
		}
		$reqOptions = Options::get('packages.userpanel.register');
		$user->type = $reqOptions['type'];
		$user->status = $reqOptions['status'] ?? User::active;
		$user->password_hash($inputs['password']);
		unset($inputs['password']);
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
		$view = View::byName(views\Register::class);
		$view->setData(Country::get(), 'countries');
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
		$inputs = array(
			'name' => array(
				'type' => 'string'
			),
			'lastname' => array(
				'type' => 'string'
			),
			'email' => array(
				'type' => 'email'
			),
			'password' => array(
				'type' => 'string',
				'htmlTags' => true,
				'multiLine' => true,
			),
			'country' => array(
				'type' => Country::class,
			),
			'city' => array(
				'type' => 'string'
			),
			'address' => array(
				'type' => 'string'
			),
			'zip' => array(
				'type' => 'number'
			),
			'phone' => array(
				'type' => 'number'
			),
			'cellphone' => array(
				'type' => 'cellphone'
			)
		);
		try {
			$user = $this->register_helper($inputs);
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
