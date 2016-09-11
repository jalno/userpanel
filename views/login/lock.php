<?php
namespace packages\userpanel\views;
use \packages\userpanel\user;
class lock extends form{
	public function setUser(user $user){
		$this->setData($user, 'user');
	}
	public function getUser(){
		return $this->getData('user');
	}
	public function getUserData($key){
		return($this->getUser()->$key);
	}
}
