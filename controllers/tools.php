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
use \packages\userpanel\usertype_permission;

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
	public function permission($data){
		authorization::haveOrFail('permission_list');
		$view = view::byName("\\packages\\userpanel\\views\\tools\\permission");
		//$permission = new usertype_permission;
		if(isset($data['type'])){
			//$permission->where("type", $data['type']);
			db::where("type", $data['type']);
		}
		$permission = db::get("userpanel_usertypes_permissions", null, array("userpanel_usertypes_permissions.*"));
		$permissions = array();
		foreach($permission as $row){
			$permissions[] = new usertype_permission($row);
		}
		$view->setPermissions($permissions);
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
