<?php
namespace packages\userpanel\controllers;

use packages\base\{Cache, db, view\Error, Exception, views\FormError, http, InputValidationException, NotFound, Options};
use packages\notifications\events\Channels;
use packages\userpanel;
use packages\userpanel\{Authentication, Controller, Date, events, Log, resetpwd\Token, User, View, views};

class resetpwd  extends Controller {

	protected $authentication = false;

	public function view() {
		$view = View::byName(views\resetpwd::class);
		$view->setData($this->getChannelsNames(), "channelsnames");
		$this->response->setView($view);
		try {
			$this->haveChance();
		} catch (losingChance $error) {
			$error = $this->losingChanceError();
			$view->addError($error);
			$this->response->setStatus(false);
			return $this->response;
		}
		$inputs = $this->checkinputs(array(
			'username' => array(
				'type' => array('email', 'cellphone'),
				'optional' => true
			),
			'method' => array(
				'type' => 'string',
				'values' => $this->getChannelsNames(),
				'optional' => true
			),
		));
		foreach (array("username", "method") as $item) {
			if (isset($inputs[$item])){
				$view->setDataForm($inputs[$item], $item);
			}
		}
		$this->response->setStatus(true);
		return $this->response;
	}
	public function reset() {
		$view = View::byName(views\resetpwd::class);
		$view->setData($this->getChannelsNames(), "channelsnames");
		$this->response->setView($view);
		$inputsRules = array(
			'username' => array(
				'type' => array('email', 'cellphone'),
			),
			'method' => array(
				'type' => 'string',
				'values' => $this->getChannelsNames(),
			),
		);
		$view->setDataForm($this->inputsvalue($inputsRules));
		try {
			$this->haveChance();
		} catch (losingChance $error) {
			$error = $this->losingChanceError();
			$view->addError($error);
			$this->response->setStatus(false);
			return $this->response;
		}
		$inputs = $this->checkinputs($inputsRules);
		$parenthesis = new db\Parenthesis();
		$parenthesis->where("email", $inputs['username']);
		$parenthesis->orwhere("cellphone", $inputs['username']);
		$user = (new User)->where($parenthesis)->getOne();
		if(!$user){
			$this->loseOneChance();
			throw new InputValidationException('username');
		}
		$this->notifyUser($user, $inputs["method"]);
		$this->response->setData($inputs['username'], 'username');
		$this->response->setStatus(true);
		return $this->response;
	}
	public function authenticationToken() {
		$view = view::byName(views\resetpwd::class);
		$this->response->setView($view);
		try {
			$this->haveChance();
		} catch (losingChance $error) {
			$error = $this->losingChanceError();
			$view->addError($error);
			$this->response->setStatus(false);
			return $this->response;
		}
		$inputsRules = [
			'token' => [
				'type' => 'number'
			],
			'username' => [
				'type' => 'cellphone'
			]
		];
		$inputs = $this->checkinputs($inputsRules);
		$user = new User();
		$user->where('cellphone', $inputs['username']);
		if(!$user = $user->getOne()){
			throw new NotFound();
		}
		$token = new Token();
		$token->where('user', $user->id);
		$token->where('sent_at', Date::time() - 7200, '>');
		$token->where('token', $inputs['token']);
		$token->orderBy('sent_at', 'DESC');
		if(!$token = $token->getOne()){
			throw new InputValidationException('token');
		}
		Login::doLogin($user);
		$token->delete();
		$this->response->setStatus(true);
		$this->response->Go(userpanel\url('resetpwd/newpwd'));
		return $this->response;
	}
	public function authenticationEmailToken($data) {
		try {
			$this->loseOneChance();
		} catch (losingChance $error) {
			throw new NotFound();
		}
		$token = new Token();
		$token->where('token', $data['token']);
		$token->where('sent_at', Date::time() - 86400, '>');
		if (!$token = $token->getOne()) {
			throw new NotFound();
		}
		Login::doLogin($token->user);
		$token->delete();
		$this->response->setStatus(true);
		$this->response->Go(userpanel\url('resetpwd/newpwd'));
		return $this->response;
	}
	private function getChannels() {
		$channels = new Channels();
		$channels->trigger();
		return $channels->get();
	}
	private function getChannelByName(string $name) {
		foreach ($this->getChannels() as $channel) {
			if ($channel->getName() == $name) {
				return $channel;
			}
		}
		return null;
	}
	private function getChannelsNames(): array {
		$names = array();
		foreach ($this->getChannels() as $channel) {
			$names[] = $channel->getName();
		}
		return $names;
	}
	private function haveChance() {
		$cache = new Cache();
		$times = Options::get('userpanel.resetpwd.mis-chance.count');
		if ($cache->get("packages.userpanel.resetpwd.bruteforce." . http::$client['ip']) > $times) {
			throw new losingChance();
		}
	}
	private function losingChanceError() {
		$period = Options::get('userpanel.resetpwd.mis-chance.period');
		$times = Options::get('userpanel.resetpwd.mis-chance.count');
		$error = new Error();
		$error->setCode('userpanel.resetpwd.losingChance');
		$error->setMessage(t('error.userpanel.resetpwd.losingChance', [
			'times' => $times,
			'expire_at' => Date::relativeTime(Date::time() + $period)
		]));
		return $error;
	}
	private function loseOneChance() {
		$cache = new Cache();
		$period = Options::get('userpanel.resetpwd.mis-chance.period');
		if(!$count = $cache->get("packages.userpanel.resetpwd.bruteforce.".http::$client['ip'])){
			$count = 1;
		}
		$cache->set("packages.userpanel.resetpwd.bruteforce." . http::$client['ip'], $count + 1, $period);
	}
	private function notifyUser(User $user, string $method) {
		$token = new Token();
		$token->token = ($method == "sms") ? rand(10000, 99999) : md5(rand(1000, 999999));
		$token->user = $user->id;
		$token->ip = http::$client['ip'];
		$this->getChannelByName($method)->notify(events\ResetPWD($token));
		$token->sent_at = Date::time();
		$token->save();
	}
}
class losingChance extends Exception {}