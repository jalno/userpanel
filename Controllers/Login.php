<?php

namespace packages\userpanel\Controllers;

use packages\base\DB;
use packages\base\HTTP;
use packages\base\InputValidationException;
use packages\base\Options;
use packages\base\Response;
use packages\base\Session;
use packages\base\View\Error;
use packages\userpanel;
use packages\userpanel\Authentication;
use packages\userpanel\Controller;
use packages\userpanel\Country;
use packages\userpanel\Date;
use packages\userpanel\Events;
use packages\userpanel\Exceptions\UserIsNotActiveException;
use packages\userpanel\Log;
use packages\userpanel\Logs;
use packages\userpanel\Register\RegisterField;
use packages\userpanel\Register\RegisterFields;
use packages\userpanel\User;
use packages\userpanel\View;
use packages\userpanel\Views;

class Login extends Controller
{
    /**
     * @var bool indicates that these methods does not need authentication and they can handle themselves
     */
    protected $authentication = false;

    /**
     * Save givin user in Authentication class and set handler to session.
     * It's also save a log of successfull login database.
     */
    public static function doLogin(User $user): void
    {
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
            if (false === $key) {
                $handler->addPreviousUser($prevUser);
            } else {
                $len = count($prevUsers);
                for ($i = $key; $i < $len; ++$i) {
                    $handler->popPreviousUser();
                }
            }
        }

        $log = new Log();
        $log->user = $user->id;
        $log->title = t('logs.login');
        $log->type = Logs\Login::class;
        $log->save();
    }

    public static function getRegisterRules(): array
    {
        $inputs = [
            'password' => [
                'type' => 'string',
                'htmlTags' => true,
                'multiLine' => true,
            ],
        ];
        foreach (RegisterFields::actives(true) as $field) {
            switch ($field) {
                case RegisterField::EMAIL:
                    $inputs[$field->value] = [
                        'type' => 'email',
                        'optional' => $field->isOptional(),
                    ];
                    break;
                case RegisterField::CELLPHONE:
                    $inputs[$field->value] = [
                        'type' => 'cellphone',
                        'optional' => $field->isOptional(),
                    ];
                    break;
                case RegisterField::PHONE:
                    $inputs[$field->value] = [
                        'type' => 'phone',
                        'optional' => $field->isOptional(),
                    ];
                    break;
                case RegisterField::COUNTRY:
                    $inputs[$field->value] = [
                        'type' => Country::class,
                        'optional' => $field->isOptional(),
                    ];
                    break;
                case RegisterField::ZIP:
                    $inputs[$field->value] = [
                        'type' => 'number',
                        'optional' => $field->isOptional(),
                    ];
                    break;
                default:
                    $inputs[$field->value] = [
                        'type' => 'string',
                        'optional' => $field->isOptional(),
                    ];
                    break;
            }
        }

        return $inputs;
    }

    /**
     * Get remember token from the cookies and find the active user.
     */
    public static function checkRememberToken(): ?User
    {
        $token = HTTP::$request['cookies']['remember'] ?? null;
        if (!$token or !is_string($token)) {
            return null;
        }

        return (new User())
                ->where('remember_token', $token)
                ->where('status', User::active)
                ->getOne();
    }

    protected Authentication\BruteForceThrottle $bruteForceThrottle;

    public function __construct()
    {
        parent::__construct();

        /** @var false|array{'period'?:int,'count'?:int}|null $throttleOptions */
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
     */
    public function login_helper(): User
    {
        $this->bruteForceThrottle->mustHasChance();

        $inputs = $this->checkinputs([
            'credential' => [
                'type' => ['email', 'cellphone'],
            ],
            'password' => [
                'type' => 'string',
                'htmlTags' => true,
                'multiLine' => true,
            ],
            'remember' => [
                'type' => 'bool',
                'optional' => true,
            ],
        ]);
        $p = new DB\Parenthesis();
        $p->where('email', $inputs['credential']);
        $p->orwhere('cellphone', $inputs['credential']);
        $user = (new User())
                    ->where($p)
                    ->getOne();
        if (!$user) {
            $this->bruteForceThrottle->loseOneChance();
            throw new InputValidationException('credential');
        }
        if (!$user->password_verify($inputs['password'])) {
            $log = new Log();
            $log->title = t('log.wrongLogin');
            $log->type = Logs\WrongLogin::class;
            $log->user = $user->id;
            $log->parameters = [
                'user' => $user,
                'wrongpaswd' => $inputs['password'],
            ];
            $log->save();

            $this->bruteForceThrottle->loseOneChance();
            throw new InputValidationException('password');
        }
        (new Events\BeforeLogin())->trigger();
        if (User::active == $user->status) {
            self::doLogin($user);
            if (isset($inputs['remember']) and $inputs['remember']) {
                HTTP::setcookie('remember', $user->createRememberToken(), Date::time() + 31536000);
            }
        } else {
            throw new UserIsNotActiveException($user->status);
        }

        return $user;
    }

    /**
     * Show login view.
     */
    public function loginView(): Response
    {
        $this->response->setStatus(false);

        $backTo = (isset(HTTP::$data['backTo'])
            and is_string(HTTP::$data['backTo'])
            and HTTP::$data['backTo']
            and HTTP::is_safe_referer(HTTP::$data['backTo'])
        ) ? HTTP::$data['backTo'] : '';

        $view = View::byName(Views\Login::class);
        $view->setDataForm($backTo, 'backTo');
        $view->setCountries((new Country())->get());
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
                $this->response->Go($backTo ?: userpanel\Url());
            }
        }

        $this->response->setStatus(true);

        return $this->response;
    }

    /**
     * @throws InputValidationException with empty input field
     */
    public function login(): Response
    {
        $view = View::byName(Views\Login::class);
        $backTo = (isset(HTTP::$data['backTo'])
                    and is_string(HTTP::$data['backTo'])
                    and HTTP::$data['backTo']
                    and HTTP::is_safe_referer(HTTP::$data['backTo'])) ? HTTP::$data['backTo'] : '';
        $view->setDataForm($backTo, 'backTo');
        $this->response->setView($view);
        $this->response->setStatus(false);

        try {
            $user = $this->login_helper();
            $this->response->setStatus(true);
            $this->response->Go($backTo ? $backTo : userpanel\Url());
        } catch (UserIsNotActiveException $e) {
            $error = '';
            switch ($e->getStatus()) {
                case User::deactive:
                    $error = 'user_status_is_deactive_in_login';
                    break;
                case User::suspend:
                    $error = 'user_status_is_suspend_in_login';
                    break;
            }
            $this->response->setData([
                'error' => [
                    [
                        'type' => Error::FATAL,
                        'error' => $error,
                    ],
                ],
            ]);
        } catch (InputValidationException $e) {
            $e->setInput('');
            throw $e;
        }

        return $this->response;
    }

    /**
     * Reset authentication and redirect user to login page.
     */
    public function logout(): Response
    {
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
        HTTP::removeCookie('remember');
        $this->response->Go(userpanel\Url('login'));

        return $this->response;
    }

    /**
     * Save new user based on inputs.
     * Also save new log.
     */
    public function register_helper(array $ruls): User
    {
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
        (new Events\BeforeRegister())->trigger();
        $user->save();

        if (User::active == $user->status) {
            Authentication::setUser($user);
            $handler = new Authentication\SessionHandler();
            $handler->setSession();
            $handler->unlock();
            Authentication::setHandler($handler);

            $log = new Log();
            $log->title = t('log.register');
            $log->type = Logs\Register::class;
            $log->user = $user->id;
            $log->parameters = [
                'user' => $user,
                'inputs' => $inputs,
            ];
            $log->save();
        } else {
            throw new UserIsNotActiveException($user->status);
        }

        return $user;
    }

    /**
     * Show register form.
     */
    public function signup(): Response
    {
        /** @var Views\Register $view */
        $view = View::byName(Views\Register::class);
        $view->setCountries(Country::get());
        $view->setRegisterFields(RegisterFields::actives(true));
        $this->response->setStatus(true);
        $this->response->setView($view);

        return $this->response;
    }

    /**
     * Register new user.
     *
     * @return Response
     */
    public function register()
    {
        $view = View::byName(Views\Register::class);
        $this->response->setView($view);
        $view->setData(Country::get(), 'countries');
        $this->response->setStatus(false);
        $rules = self::getRegisterRules();
        try {
            $user = $this->register_helper($rules);
            $this->response->setStatus(true);
            $this->response->Go(userpanel\url());
        } catch (UserIsNotActiveException $e) {
            $error = '';
            switch ($e->getStatus()) {
                case User::deactive:
                    $error = 'user_status_is_deactive_in_register';
                    break;
                case User::suspend:
                    $error = 'user_status_is_suspend_in_register';
                    break;
            }
            $this->response->setData([
                'error' => [
                    [
                        'type' => Error::FATAL,
                        'error' => $error,
                    ],
                ],
            ]);
        }

        return $this->response;
    }
}
