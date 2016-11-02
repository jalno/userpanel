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
	/*
		class hay usertype_permission, usertype ke bazi jaha comment hastand, meghdar null barmigardunan
		bjach az db estefade kardam, ke dar surati ke moshkel kelesa ya kolan meghdar bdan, ba class ha
		baznevisi mikunam
		bardasht az harfat ina hast, age moghayerati daran, dorstesh mikunam
		bdun dava...
		ba bardasht tik ha dar safe edit va submit form, hazf anjam mishe
	*/
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
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function usertype_edit($data){
		authorization::haveOrFail('usertype_edit');
		$view = view::byName("\\packages\\userpanel\\views\\tools\\usertype_edit");
		db::where("id", $data['type']);
		$usertype = new usertype(db::getOne("userpanel_usertypes", array("userpanel_usertypes.*")));
		$view->setUserType($usertype);
		db::orderBy("id", "ASC");
		$allusertype = db::get("userpanel_usertypes", null, array("userpanel_usertypes.*"));
		$usertypes = array();
		foreach($allusertype as $utype){
			$usertypes[] = new usertype($utype);
		}
		$view->setUserTypes($usertypes);
		$this->response->setStatus(false);

		if(http::is_post()){
			// inputsRules for title of usertype
			$inputUsertype = array(
				'title' => array(
					'type' => 'string',
					'optional' => true
				)

			);
			// END
			// inputsRules for title of permissions
			$inputsPermissions = array();
			for($i = 1; $i <= count($usertype->permissions); $i++){
				$inputsPermissions["permission({$i})"] = array(
					'type' => 'string',
					'optional' => true
				);
			}
			// END
			// inputsRules for title of priority
			$inputsPriority = array();
			for($x = 1; $x <= count($usertype->children); $x++){
				$inputsPriority["priority({$x})"] = array(
					'type' => 'string',
					'optional' => true
				);
			}
			// END
			try{
				// checkinputs for usertype values
				$inputsUsertype = $this->checkinputs($inputUsertype);
				// checkinputs for permissions values
				$permissions = $this->checkinputs($inputsPermissions);
				// checkinputs for priorities
				$priorities = $this->checkinputs($inputsPriority);
				// Processing of usertypes values
				if(isset($inputsUsertype)){
					db::where("id", $usertype->id);
					db::update("userpanel_usertypes", array(
						'title' => $inputsUsertype['title']
					));
				}
				// Processing for permissions values
				$permissioNames = array();
				foreach($usertype->permissions as $permission){
					$permissioNames[] = $permission->name;
				}
				// number of row mustbe delete
				$permissiosDdelete = array_diff($permissioNames, $permissions);
				// Processing of deleting permissions
				if(!empty($permissiosDdelete)){
					//$usertype_permission = usertype_permission::where('name', array_values($permissiosDdelete), 'in');
					//$usertype_permission->delete();
					db::where('name', array_values($permissiosDdelete), 'in');
					db::delete("userpanel_usertypes_permissions");
				}
				// Processing for priorities values
				$priorityIds = array();
				foreach($usertype->children as $priority){
					$priorityIds[] = $priority->childdata['id'];
				}
				// number of row mustbe delete
				$prioritiesDelete = array_diff($priorityIds, $priorities);
				// Processing of deleting priorities
				if(!empty($prioritiesDelete)){
					//$usertype_permission = usertype_permission::where('name', array_values($prioritiesDelete), 'in');
					//$usertype_permission->delete();
					db::where('child', array_values($prioritiesDelete), 'in');
					db::delete("userpanel_usertypes_priorities");
				}
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("tools/usertype/edit/".$usertype->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function priority_add($data){
		$inputsRules = array(
			'parent' => array(
				'type' => 'number'
			),
			'child' => array(
				'type' => 'number'
			)
		);
		try{
			$inputs = $this->checkinputs($inputsRules);
			db::insert("userpanel_usertypes_priorities", array(
				'parent' => $inputs['parent'],
				'child' => $inputs['child']
			));
		}catch(inputValidation $error){
		}
		$this->response->Go(userpanel\url("tools/usertype/edit/".$inputs['parent']));
		$this->response->setStatus(true);
		return $this->response;
	}
	public function priority_edit($data){
		$inputsRules = array(
			'parent' => array(
				'type' => 'number'
			),
			'child' => array(
				'type' => 'number'
			)
		);
		try{
			$inputs = $this->checkinputs($inputsRules);
			db::where("parent", $inputs['parent']);
			db::where("child", $data['child']);
			db::update("userpanel_usertypes_priorities", array(
				'child' => $inputs['child']
			));
		}catch(inputValidation $error){
		}
		$this->response->Go(userpanel\url("tools/usertype/edit/".$inputs['parent']));
		$this->response->setStatus(true);
		return $this->response;
	}
	public function permission_add(){
		$inputsRules = array(
			'type' => array(
				'type' => 'number'
			),
			'name' => array(
				'type' => 'string'
			)
		);
		try{
			$inputs = $this->checkinputs($inputsRules);
			db::insert("userpanel_usertypes_permissions", array(
				'type' => $inputs['type'],
				'name' => $inputs['name']
			));
		}catch(inputValidation $error){
		}
		$this->response->Go(userpanel\url("tools/usertype/edit/".$inputs['type']));
		$this->response->setStatus(true);
		return $this->response;
	}
	public function permission_edit($data){
		$inputsRules = array(
			'type' => array(
				'type' => 'number'
			),
			'name' => array(
				'type' => 'string'
			)
		);
		try{
			$inputs = $this->checkinputs($inputsRules);
			db::where("type", $inputs['type']);
			db::where("name", $data['name']);
			db::update("userpanel_usertypes_permissions", array(
				'name' => $inputs['name']
			));
		}catch(inputValidation $error){
		}
		$this->response->Go(userpanel\url("tools/usertype/edit/".$inputs['type']));
		$this->response->setStatus(true);
		return $this->response;
	}
}
