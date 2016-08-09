<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\userpanel\controller;
use \packages\userpanel\view;

class dashboard extends controller{
	protected $authentication = true;
	function index(){
		if($view = view::byName("\\packages\\userpanel\\views\\dashboard")){
			$this->response->setView($view);
			return $this->response;
		}
	}
}
