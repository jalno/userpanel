<?php
namespace packages\userpanel\views\settings\usertypes;
use \packages\userpanel\views\form;
use \packages\userpanel\usertype;
class edit extends form{
	public function setUserType(usertype $usertype){
		$this->setData($usertype, 'usertype');
	}
	public function getUserType(){
		return $this->getData('usertype');
	}
	public function setChildrenTypes($usertypes){
		$this->setData($usertypes, 'childrentypes');
	}
	public function getChildrenTypes(){
		return $this->getData('childrentypes');
	}
	public function setPermissions($permissions){
		$this->setData($permissions, 'permissions');
	}
	public function getPermissions(){
		return $this->getData('permissions');
	}
}
