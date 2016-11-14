<?php
namespace packages\userpanel\views\profile;
class view extends \packages\userpanel\view{
	public function setUserData($data){
		$this->setData($data, 'user');
	}
	public function getUserData($key){
		return($this->data['user']->$key);
	}
}
