<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\userpanel;
use \packages\userpanel\controller;
use \packages\userpanel\view;
use \packages\userpanel\user;
use \packages\userpanel\authentication;
use \packages\base\inputValidation;

class login  extends controller{
	protected $authentication = false;
	public function loginForm(){
		if($view = view::byName("\\packages\\userpanel\\views\\login")){
			$this->response->setView($view);
			return $this->response;
		}
	}
	public function submitForm(){
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
		$response = $this->loginForm();
		$response->setStatus(false);
		try{
			$inputs = $this->checkinputs($inputs);
			$user =  new user();
			$user->where("email", $inputs['username']);
			$user->orwhere("cellphone", $inputs['username']);
			if(
				$user->getOne() and
				$user->password_verify($inputs['password'])
			){

				authentication::setUser($user);
				authentication::setSession();
				$response->setStatus(true);
				$response->go(base\url('userpanel'));

			}else{
				$response->setData(array('error' => 'invalid'));
			}
		}catch(inputValidation $error){
			$response->setData(array('error' => $error->getInput()));
		}
		return $response;
	}
	public function logout(){
		authentication::unsetSession();
		$this->response->Go(userpanel\url('login'));
		return $this->response;
	}
}
