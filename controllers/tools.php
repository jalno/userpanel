<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\base\db;
use \packages\base\http;
use \packages\base\db\parenthesis;
use \packages\base\views\FormError;
use \packages\base\inputValidation;
use \packages\base\db\InputDataType;
use \packages\base\db\duplicateRecord;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\view;
use \packages\userpanel\usertype;
use \packages\userpanel\controller;
use \packages\userpanel\authorization;
use \packages\userpanel\authentication;

class tools extends controller{
	protected $authentication = true;
	public function usertype(){
		authorization::haveOrFail('usertype_list');
		$view = view::byName("\\packages\\userpanel\\views\\tools\\usertype");
		//$usertypes = usertype::orderBy("id", "ASC");
		db::orderBy("id", "ASC");
		$usertype = db::get("userpanel_usertypes", null, array("userpanel_usertypes.*"));
		$usertypes = array();
		foreach($usertype as $utype){
			$usertypes[] = new usertype($utype);
		}
		$view->setUserTypes($usertypes);
		$this->response->setView($view);
		return $this->response;
	}
}
