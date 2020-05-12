<?php
namespace packages\userpanel\controllers;

use packages\base\{View, Session, http, InputValidationException};
use packages\userpanel;
use packages\userpanel\{User, Authentication, Log, Controller, views, logs};

class Lock extends Controller {
	/**
	 * @var bool indicates that these methods does not need authentication and they can handle themselves.
	 */
	protected $authentication = false;
	
	/**
	 * Lockdown the session authencticated user.
	 * 
	 * @return Response
	 */
	public function lockdown() {
		$session = new Authentication\SessionHandler();
		if (!$session->getSession()) {
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url('login'));
			return $this->response;
		}
		$session->lock();
		$user = (new User)->byID($session->getUserID());
		$view = View::byName(views\Lock::class);
		$view->setUser($user);
		if (http::is_safe_referer()) {
			Session::set('loginto', http::$request['referer']);
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
	public function unlock() {
		$session = new Authentication\SessionHandler();
		if (!$session->getSession()) {
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url('login'));
			return $this->response;
		}
		$user = (new User)->byID($session->getUserID());
		$view = View::byName(views\Lock::class);
		$view->setUser($user);
		$this->response->setView($view);
		if ($user->status != User::active) {
			throw new InputValidationException("password");
		}
		$inputs = $this->checkinputs(array(
			'password' => array(
				'type' => 'string',
				'htmlTags' => true,
				'multiLine' => true,
			)
		));
		if (!$user->password_verify($inputs['password'])) {
			$log = new Log();
			$log->title = t("log.wrongLogin");
			$log->type = logs\wrongLogin::class;
			$log->user = $user->id;
			$log->parameters = [
				'user' => $user,
				'wrongpaswd' => $inputs['password']
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
