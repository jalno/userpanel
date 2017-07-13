<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\base\options;
use \packages\base\http;
use \packages\base\session;
use \packages\base\InputDataType;
use \packages\base\inputValidation;
use \packages\base\db\duplicateRecord;
use \packages\base\views\FormError;
use \packages\userpanel;
use \packages\userpanel\controller;
use \packages\userpanel\view;
use \packages\userpanel\log;
use \packages\userpanel\user;
use \packages\userpanel\authentication;
use \packages\userpanel\country;

class login  extends controller{
	protected $authentication = false;
	function login_helper($inputsRules){

		$inputs = $this->checkinputs($inputsRules);
		$user = new user();
		$user->where("email", $inputs['username']);
		$user->orwhere("cellphone", $inputs['username']);
		$user->getOne();
		if($user->id){
			if($user->password_verify($inputs['password'])){
				authentication::setUser($user);
				authentication::setSession();
				authentication::unlockSession();
				$log = new log();
				$log->type = log::login;
				$log->users = array($user->id);
				$log->params = array(
					'user' => $user->id
				);
				$log->save();
				return $user;
			}else{
				$log = new log();
				$log->type = log::loginwrong;
				$log->users = array($user->id);
				$log->params = array(
					'user' => $user->id,
					'wrongpaswd' => $inputs['password']
				);
				$log->save();
				throw new inputValidation('password');
			}
		}else{
			throw new inputValidation('username');
		}
	}
	public function login(){
		if(!authentication::getSession()){
			if($view = view::byName("\\packages\\userpanel\\views\\login")){

				if(http::is_post()){
					$inputs = array(
						'username' => array(
							'type' => array('email', 'cellphone'),
						),
						'password' => array(),
						'remmeber' => array(
							'optional' => true,
							'type' => 'bool',
							'default' => false
						)
					);
					try{
						$this->response->setStatus(false);
						$user = $this->login_helper($inputs);
						$this->response->setStatus(true);
						$loginto = session::get('loginto');
						session::unsetval('loginto');
						$this->response->Go($loginto ? $loginto : userpanel\url());
					}catch(inputValidation $error){
						$error->setInput('');
						$view->setFormError(FormError::fromException($error));
					}
				}else{
					$this->response->setStatus(true);
					if(http::is_safe_referer()){
						session::set('loginto', http::$request['referer']);
					}
				}
				$this->response->setView($view);
			}
		}else{
			authentication::unlockSession();
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url());
		}
		return $this->response;
	}
	public function logout(){
		authentication::unsetSession();
		$this->response->Go(userpanel\url('login'));
		return $this->response;
	}
	public function lock(){
		if(authentication::getSession()){
			if($view = view::byName("\\packages\\userpanel\\views\\lock")){
				$user = authentication::getUser();
				$view->setUser($user);
				if(http::is_post()){

					$this->response->setStatus(false);
					$inputsRules = array(
						'password' => array(),
					);
					try{
						$inputs = $this->checkinputs($inputsRules);
						if($user->password_verify($inputs['password'])){
							authentication::unlockSession();
							$this->response->setStatus(true);
							$loginto = session::get('loginto');
							session::unsetval('loginto');
							$this->response->Go($loginto ? $loginto : userpanel\url());
						}else{
							$log = new log();
							$log->type = log::loginwrong;
							$log->users = array($user->id);
							$log->params = array(
								'user' => $user->id,
								'wrongpaswd' => $inputs['password']
							);
							$log->save();
							throw new inputValidation('password');
						}
					}catch(inputValidation $error){
						$view->setFormError(FormError::fromException($error));
					}
					$view->setDataForm($this->inputsvalue($inputsRules));
				}else{
					authentication::lockSession();
					if(http::is_safe_referer()){
						session::set('loginto', http::$request['referer']);
					}
					$this->response->setStatus(true);
				}
				$this->response->setView($view);
			}
		}else{
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url('login'));
		}
		return $this->response;
	}
	function register_helper($inputsRules){

		$inputs = $this->checkinputs($inputsRules);
		$user = new user($inputs);
		$user->type = options::get('packages.userpanel.register')['type'];
		$user->status = 1;
		$user->password_hash($inputs['password']);
		unset($inputs['password']);
		$user->save();
		authentication::setUser($user);
		authentication::setSession();
		$log = new log();
		$log->type = log::register;
		$log->users = array($user->id);
		$log->params = array(
			'user' => $user->id,
			'inputs' => $inputs
		);
		$log->save();
		return $user;
	}
	function register(){
		if($view = view::byName("\\packages\\userpanel\\views\\register")){
			$this->response->setStatus(false);
			$view->setData(country::get(), 'countries');
			if(http::is_post()){
				$inputsRules = array(
					'name' => array(
						'type' => 'string'
					),
					'lastname' => array(
						'type' => 'string'
					),
					'email' => array(
						'type' => 'email'
					),
					'password' => array(),
					'country' => array(
						'type' => 'number'
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
				try{
					$user = $this->register_helper($inputsRules);
					$this->response->setStatus(true);
					$this->response->Go(userpanel\url());
				}catch(inputValidation $error){
					$view->setFormError(FormError::fromException($error));
				}catch(InputDataType $error){
					$view->setFormError(FormError::fromException($error));
				}catch(duplicateRecord $error){
					$view->setFormError(FormError::fromException($error));
				}
				$view->setDataForm($this->inputsvalue($inputsRules));
			}else{
				$this->response->setStatus(true);
			}
			$this->response->setView($view);
			return $this->response;
		}
	}
}
