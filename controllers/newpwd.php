<?php
namespace packages\userpanel\controllers\resetpwd;
use \packages\base\{inputValidation, db\InputDataType, db\parenthesis, views\FormError, translator};

use \packages\userpanel;
use \packages\userpanel\{user, authorization, authentication, controller, view, log, logs};

class newpwd extends controller{
	protected $authentication = true;
	public function edit(){
		authorization::haveOrFail('resetpwd_newpwd');
		$view = view::byName("\\packages\\userpanel\\views\\resetpwd\\newpwd");
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function update(){
		authorization::haveOrFail('resetpwd_newpwd');
		$view = view::byName("\\packages\\userpanel\\views\\resetpwd\\newpwd");
		$inputsRules = [
			'password' => [
				'type' => 'string'
			],
			'password2' => [
				'type' => 'string'
			]
		];
		try{
			$this->response->setStatus(false);
			$inputs = $this->checkinputs($inputsRules);
			if($inputs['password'] != $inputs['password2']){
				throw new inputValidation('dontmatch');
			}
			$user = authentication::getUser();
			$user->password_hash($inputs['password']);
			unset($inputs['password'], $inputs['password2']);
			$user->save();

			$log = new log();
			$log->title = translator::trans("log.resetPWD");
			$log->type = logs\userEdit::class;
			$log->user = $user->id;
			$log->parameters = [
				'oldData' => [
					'password' => "***"
				]
			];
			$log->save();

			$this->response->setStatus(true);
			$this->response->Go(userpanel\url());
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
		}catch(passwordDontMatch $error){
			$view->setFormError(FormError::fromException($error));
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$this->response->setView($view);
		return $this->response;
	}
}