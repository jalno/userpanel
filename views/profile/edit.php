<?php
namespace packages\userpanel\views\profile;
use \packages\userpanel\views\form;
use \packages\base\packages;
class edit extends form{
	public function setCountries($countries){
		$this->setData($countries, 'countries');
	}
	public function getCountries(){
		return $this->getData('countries');
	}
	public function setUserData($data){
		$this->setData($data, 'user');
	}
	public function getUserData($key){
		return($this->data['user']->$key);
	}
	public function setForm(){
		$user = $this->getData('user');
		$this->setDataForm($user->toArray());
		foreach($user->socialnetworks as $socialnet){
			$this->setDataForm($socialnet->username, 'socialnets['.$socialnet->network.']');
		}
	}
}
