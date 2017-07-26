<?php
namespace packages\userpanel\controllers;
use \packages\base\http;
use \packages\base\cache;
use \packages\base\options;
use \packages\base\NotFound;
use \packages\base\translator;
use \packages\base\view\error;
use \packages\base\inputValidation;
use \packages\base\views\FormError;
use \packages\userpanel;
use \packages\userpanel\date;
use \packages\userpanel\controller;
use \packages\userpanel\view;
use \packages\userpanel\log;
use \packages\userpanel\user;
use \packages\sms\api as smsAPI;
use \packages\email\api as emailAPI;
use \packages\userpanel\authentication;
use \packages\userpanel\resetpwd\token;
class resetpwd  extends controller{
	protected $authentication = false;
	public function view(){
		$view = view::byName("\\packages\\userpanel\\views\\resetpwd");
		$inputsRules = [
			'username' => [
				'type' => ['email', 'cellphone'],
				'optional' => true
			],
			'method' => [
				'type' => 'string',
				'values' => ['sms', 'email'],
				'optional' => true
			]
		];
		try{
			$this->haveChance();
			$inputs = $this->checkinputs($inputsRules);
			if(isset($inputs['username'])){
				$view->setDataForm($inputs['username'], 'username');
			}
			if(isset($inputs['method'])){
				$view->setDataForm($inputs['method'], 'method');
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
		}catch(losingChance $error){
			$error = $this->losingChanceError();
			$view->addError($error);
		}
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	private function haveChance(){
		$cache = new cache();
		$times = options::get('userpanel.resetpwd.mis-chance.count');
		if($cache->get("packages.userpanel.resetpwd.bruteforce.".http::$client['ip']) > $times){
			throw new losingChance();
		}
	}
	private function losingChanceError(){
		$period = options::get('userpanel.resetpwd.mis-chance.period');
		$times = options::get('userpanel.resetpwd.mis-chance.count');
		$error = new error();
		$error->setCode('userpanel.resetpwd.losingChance');
		$error->setMessage(translator::trans('error.userpanel.resetpwd.losingChance', [
			'times' => $times,
			'expire_at' => date::relativeTime(date::time() + $period)
		]));
		return $error;
	}
	private function loseOneChance(){
		$cache = new cache();
		$period = options::get('userpanel.resetpwd.mis-chance.period');
		if(!$count = $cache->get("packages.userpanel.resetpwd.bruteforce.".http::$client['ip'])){
			$count = 1;
		}
		$cache->set("packages.userpanel.resetpwd.bruteforce.".http::$client['ip'], $count + 1, $period);
	}
	public function reset(){
		$view = view::byName("\\packages\\userpanel\\views\\resetpwd");
		$inputsRules = [
			'username' => [
				'type' => ['email', 'cellphone']
			],
			'method' => [
				'type' => 'string',
				'values' => ['sms', 'email']
			]
		];
		try{
			$this->haveChance();
			$this->response->setStatus(true);
			$inputs = $this->checkinputs($inputsRules);
			switch($inputs['method']){
				case('sms'):
					$user = new user();
					$user->where('cellphone', $inputs['username']);
					if(!$user = $user->getOne()){
						$this->loseOneChance();
						throw new inputValidation('username');
					}
					$this->sendSMS($user);
					break;
				case('email'):
					$user = new user();
					$user->where('email', $inputs['username']);
					if(!$user = $user->getOne()){
						$this->loseOneChance();
						throw new inputValidation('username');
					}
					$this->sendEmail($user);
					break;
			}
			$this->response->setData($inputs['username'], 'username');
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}catch(losingChance $error){
			$error = $this->losingChanceError();
			$view->addError($error);
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$this->response->setView($view);
		return $this->response;
	}
	private function sendSMS(user $user){
		$token = new token();
		$token->token = rand(10000, 99999);
		$token->user = $user->id;
		$token->ip = http::$client['ip'];
		$api = new smsAPI();
		$api->template('userpanel_resetpwd_token', ['token' => $token]);
		$api->to($user->cellphone, $user);
		$api->send();
		$token->sent_at = date::time();
		$token->save();
	}
	private function sendEmail(user $user){
		$token = new token();
		$token->token = md5(rand(1000, 999999));
		$token->user = $user->id;
		$token->ip = http::$client['ip'];
		$api = new emailAPI();
		$api->template('userpanel_resetpwd_token', ['token' => $token]);
		$api->to($user->email, $user);
		$api->send();
		$token->sent_at = date::time();
		$token->save();
	}
	public function authenticationToken(){
		$view = view::byName("\\packages\\userpanel\\views\\resetpwd");
		$inputsRules = [
			'token' => [
				'type' => 'number'
			],
			'username' => [
				'type' => 'cellphone'
			]
		];
		try{
			$this->haveChance();
			$this->response->setStatus(false);
			$inputs = $this->checkinputs($inputsRules);
			$user = new user();
			$user->where('cellphone', $inputs['username']);
			if(!$user = $user->getOne()){
				throw new NotFound();
			}
			$token = new token();
			$token->where('user', $user->id);
			$token->where('sent_at', date::time() - 7200, '>');
			$token->where('token', $inputs['token']);
			$token->orderBy('sent_at', 'DESC');
			if(!$token = $token->getOne()){
				throw new inputValidation('token');
			}
			$token->delete();
			login::doLogin($user);
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url('resetpwd/newpwd'));
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
		}catch(losingChance $error){
			$error = $this->losingChanceError();
			$view->addError($error);
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function authenticationEmailToken($data){
		try{
			$this->loseOneChance();
		}catch(losingChance $error){
			throw new NotFound();
		}
		$token = new token();
		$token->where('token', $data['token']);
		$token->where('sent_at', date::time() - 86400, '>');
		if(!$token = $token->getOne()){
			throw new NotFound();
		}
		login::doLogin($token->user);
		$this->response->setStatus(true);
		$token->delete();
		$this->response->Go(userpanel\url('resetpwd/newpwd'));
		return $this->response;
	}
}
class losingChance extends \Exception{}