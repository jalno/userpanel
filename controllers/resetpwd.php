<?php
namespace packages\userpanel\controllers;

use packages\base\{Cache, Response, View, View\Error, Http, InputValidationException, NotFound, Options};
use packages\notifications\{IChannel, API as NotificationAPI};
use packages\userpanel\{Controller, Date, Log, resetpwd\Token, User, views, validators, events};

use function packages\userpanel\url;

class Resetpwd  extends Controller {

	/**
	 * @var bool
	 */
	protected $authentication = false;

	/**
	 * @var IChannel[]|null
	 */
	protected $validChannels;

	public function view(): Response {
		$view = View::byName(views\Resetpwd::class);
		$view->setData($this->getChannelsNames(), "channelsnames");
		$this->response->setView($view);
		Cache::set("packages.userpanel.resetpwd.bruteforce." . http::$client['ip'],0);
		if (!$this->hasChance()) {
			$error = $this->losingChanceError();
			$view->addError($error);
		}

		$inputs = $this->checkInputs(array(
			'credential' => array(
				'type' => 'string',
				'optional' => true
			),
			'method' => array(
				'type' => 'string',
				'values' => $this->getChannelsNames(),
				'optional' => true
			),
		));
		foreach ($inputs as $key => $value) {
			$view->setDataForm($value, $key);
		}
		return $this->response;
	}

	public function reset(): Response {
		$view = View::byName(views\Resetpwd::class);
		$view->setData($this->getChannelsNames(), "channelsnames");
		$this->response->setView($view);
		$inputsRules = array(
			'credential' => array(
				'type' => validators\UserCredentialValidator::class,
			),
			'method' => array(
				'type' => 'string',
				'values' => $this->getChannelsNames(),
			),
		);
		$view->setDataForm($this->inputsvalue($inputsRules));
		(new events\BeforeResetPassword)->trigger();
		$this->mustHasChance();
		try {
			$inputs = $this->checkInputs($inputsRules);
		} catch (InputValidationException $e) {
			if ($e->getInput() == 'credential') {
				$this->loseOneChance();
			}
			throw $e;
		}

		$channel = $this->getChannelByName($inputs['method']);
		$token = new Token();
		$token->token = rand(1000, 999999);
		$token->user = $inputs['credential']->id;
		$token->ip = http::$client['ip'];
		$channel->notify(new events\ResetPWD($token));
		$token->sent_at = Date::time();
		$token->save();
		$this->response->setStatus(true);
		return $this->response;
	}

	public function token(): Response {
		$view = View::byName(views\Resetpwd::class);
		$this->response->setView($view);
		$this->mustHasChance();
		$inputs = $this->checkInputs(array(
			'token' => [
				'type' => 'number'
			],
			'credential' => [
				'type' => validators\UserCredentialValidator::class,
			]
		));
	
		$token = (new Token())
			->where('user', $inputs['credential']->id)
			->where('sent_at', Date::time() - 7200, '>')
			->where('token', $inputs['token'])
			->orderBy('sent_at', 'DESC')
			->getOne();
		if (!$token) {
			$this->loseOneChance();
			throw new InputValidationException('token');
		}

		Login::doLogin($inputs['credential']);
		$token->delete();

		$this->response->setStatus(true);
		$this->response->Go(url('resetpwd/newpwd'));
		return $this->response;
	}

	/**
	 * @return IChannel[]
	 */
	private function getChannels(): array {
		if ($this->validChannels === null) {
			$event = new events\ResetPWD(new Token());
			$this->validChannels = [];
			foreach (NotificationAPI::getChannels() as $channel) {
				if ($channel->canNotify($event)) {
					$this->validChannels[] = $channel;
				}
			}
		}
		return $this->validChannels;
	}
	
	private function getChannelByName(string $name): ?IChannel {
		foreach ($this->getChannels() as $channel) {
			if ($channel->getName() == $name) {
				return $channel;
			}
		}
		return null;
	}

	/**
	 * @return string[]
	 */
	private function getChannelsNames(): array {
		return array_map(fn($channel) => $channel->getName(), $this->getChannels());
	}

	private function hasChance(): bool {
		$times = Options::get('userpanel.resetpwd.mis-chance.count') ?? 5;
		return Cache::get("packages.userpanel.resetpwd.bruteforce." . http::$client['ip']) <= $times;
	}

	private function mustHasChance(): void {
		if (!$this->hasChance()) {
			throw $this->losingChanceError();
		}
	}

	private function losingChanceError(): Error {
		$period = Options::get('userpanel.resetpwd.mis-chance.period') ?? 3600;
		$times = Options::get('userpanel.resetpwd.mis-chance.count') ?? 5;
		$error = new Error();
		$error->setCode('userpanel.resetpwd.losingChance');
		$error->setMessage(t('error.userpanel.resetpwd.losingChance', [
			'times' => $times,
			'expire_at' => Date::relativeTime(Date::time() + $period)
		]));
		return $error;
	}

	private function loseOneChance(): void {
		$period = Options::get('userpanel.resetpwd.mis-chance.period') ?? 3600;
		$count = Cache::get("packages.userpanel.resetpwd.bruteforce.".http::$client['ip']) ?: 1;
		Cache::set("packages.userpanel.resetpwd.bruteforce." . http::$client['ip'], $count + 1, $period);
	}
}
