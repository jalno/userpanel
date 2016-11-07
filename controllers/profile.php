<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\base\http;
use \packages\base\db;
use \packages\base\inputValidation;
use \packages\base\db\duplicateRecord;
use \packages\base\db\InputDataType;
use \packages\base\db\parenthesis;
use \packages\base\views\FormError;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\usertype;
use \packages\userpanel\authorization;
use \packages\userpanel\authentication;
use \packages\userpanel\controller;
use \packages\userpanel\view;
use \packages\userpanel\country;
use \packages\userpanel\log;


class profile extends controller{
	protected $authentication = true;
	public function view($data){
		authorization::haveOrFail('profile_view');
		$view = view::byName("\\packages\\userpanel\\views\\profile\\view");
		$view->setData(authentication::getUser(), 'user');
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('profile_edit');
		$types = authorization::childrenTypes();
		$user = authentication::getUser();
		$view = view::byName("\\packages\\userpanel\\views\\profile\\edit");
		$view->setCountries(country::get());
		if(http::is_post()){
			$inputs = array(
				'name' => array(
					'optional' => true,
					'type' => 'string'
				),
				'lastname' => array(
					'optional' => true,
					'type' => 'string'
				),
				'password' => array(
					'optional' => true,
					'empty' => true
				),
				'zip' => array(
					'optional' => true,
					'type' => 'number'
				),
				'city' => array(
					'optional' => true,
					'type' => 'string'
				),
				'country' => array(
					'optional' => true,
					'type' => 'string'
				),
				'address' => array(
					'optional' => true,
					'type' => 'string'
				),
				'phone' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				)
			);
			$this->response->setStatus(false);
			try{
				$formdata = $this->checkinputs($inputs);
				if(isset($formdata['country'])){
					if(!country::byId($formdata['country'])){
						throw new inputValidation("country");
					}
				}
				if(isset($formdata['zip'])){
					if(!preg_match("/^(\\d{5}|\\d{10})$/", $formdata['zip'])){
						throw new inputValidation("zip");
					}
				}
				if(isset($formdata['password']) and $formdata['password']){
					$user->password_hash($formdata['password']);
				}
				unset($formdata['password']);
				$user->save($formdata);
				$log = new log();
				$log->type = log::user_edit;
				$log->users = array(authentication::getID());
				$log->params = array(
					'user' => $user->id,
					'inputs' => $formdata
				);
				$log->save();
				$this->response->setStatus(true);
				$view->setDataForm($user->toArray());
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
				$view->setDataForm($this->inputsvalue($inputs));
			}

		}else{
			$this->response->setStatus(true);
		}
		$view->setDataForm($user->toArray());
		$this->response->setView($view);
		return $this->response;
	}
}
