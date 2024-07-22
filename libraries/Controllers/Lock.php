<?php

namespace packages\userpanel\Controllers;

use packages\base\Http;
use packages\base\InputValidationException;
use packages\base\Session;
use packages\base\View;
use packages\userpanel;
use packages\userpanel\Authentication;
use packages\userpanel\Controller;
use packages\userpanel\Log;
use packages\userpanel\Logs;
use packages\userpanel\User;
use themes\clipone\Views;

class Lock extends Controller
{
    /**
     * @var bool indicates that these methods does not need authentication and they can handle themselves
     */
    protected $authentication = false;

    /**
     * Lockdown the session authencticated user.
     *
     * @return Response
     */
    public function lockdown()
    {
        $session = new Authentication\SessionHandler();
        $me = $session->getUserID();
        if (null === $me) {
            $this->response->setStatus(true);
            $this->response->Go(userpanel\Url('login'));

            return $this->response;
        }
        $session->lock();
        $user = (new User())->byID($me);
        $view = View::byName(Views\Lock::class);
        $view->setUser($user);
        if (HTTP::is_safe_referer()) {
            Session::set('loginto', HTTP::$request['referer']);
        }
        $this->response->setStatus(true);
        $this->response->setView($view);

        return $this->response;
    }

    /**
     * Unlock the session authencticated user.
     *
     * @return Response
     */
    public function unlock()
    {
        $session = new Authentication\SessionHandler();
        $me = $session->getUserID();
        if (null === $me) {
            $this->response->setStatus(true);
            $this->response->Go(userpanel\Url('login'));

            return $this->response;
        }
        $user = (new User())->byID($me);
        $view = View::byName(Views\Lock::class);
        $view->setUser($user);
        $this->response->setView($view);
        if (User::active != $user->status) {
            throw new InputValidationException('password');
        }
        $inputs = $this->checkinputs([
            'password' => [
                'type' => 'string',
                'htmlTags' => true,
                'multiLine' => true,
            ],
        ]);
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
            throw new InputValidationException('password');
        }

        $session->unlock();
        $this->response->setStatus(true);
        $loginto = Session::get('loginto');
        Session::unset('loginto');
        $this->response->Go($loginto ? $loginto : userpanel\url());

        return $this->response;
    }
}
