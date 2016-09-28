<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\userpanel\controller;
use \packages\userpanel\view;
use \packages\userpanel\authentication;

class dashboard extends controller{
	function index(){
		if(authentication::check()){
			if($view = view::byName("\\packages\\userpanel\\views\\dashboard")){
				$this->response->setView($view);
				return $this->response;
			}
		}else{
			parent::response(authentication::FailResponse());
		}
	}
	public function notfound(){
		authentication::check();
		$this->response->setHttpCode(404);
		if($view = view::byName("\\packages\\userpanel\\views\\notfound")){
			$this->response->setView($view);
		}
		return $this->response;
	}
}
