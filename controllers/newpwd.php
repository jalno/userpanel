<?php
namespace packages\userpanel\controllers\resetpwd;

use packages\base\{InputValidationException};
use packages\userpanel\{Authorization, Authentication, Controller, View, Log, Logs, views};
use function packages\userpanel\url;

class newpwd extends Controller {

	protected $authentication = true;

	public function edit(){
		Authorization::haveOrFail('resetpwd_newpwd');
		$view = View::byName(views\resetpwd\Newpwd::class);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}

	public function update() {
		Authorization::haveOrFail('resetpwd_newpwd');
		$view = View::byName(views\resetpwd\Newpwd::class);
		$this->response->setView($view);

		$inputs = $this->checkinputs(array(
			'password' => [
				'type' => 'string'
			],
			'password2' => [
				'type' => 'string'
			]
		));

		if ($inputs['password'] != $inputs['password2']) {
			throw new InputValidationException('dontmatch');
		}

		$user = Authentication::getUser();
		$user->password_hash($inputs['password']);
		unset($inputs['password'], $inputs['password2']);
		$user->save();

		$log = new Log();
		$log->title = t("log.resetPWD");
		$log->type = logs\UserEdit::class;
		$log->user = $user->id;
		$log->parameters = [
			'oldData' => [
				'password' => "***"
			]
		];
		$log->save();

		$this->response->setStatus(true);
		$this->response->Go(url());
		return $this->response;
	}
}