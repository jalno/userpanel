<?php
namespace packages\userpanel\views\settings\usertypes;
use \packages\userpanel\views\form;
use \packages\userpanel\usertype;
class delete extends form{
	public function setUserType(usertype $usertype){
		$this->setData($usertype, 'usertype');
	}
	public function getUserType(){
		return $this->getData('usertype');
	}
}
