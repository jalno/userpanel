<?php
namespace packages\userpanel\views\users;
use \packages\userpanel\views\form;
use \packages\userpanel\authorization;

class edit extends form{
	public function setCountries($countries){
		$this->setData($countries, 'countries');
	}
	public function getCountries(){
		return $this->getData('countries');
	}
	public function setTypes($types){
		$this->setData($types, 'types');
	}
	public function getTypes(){
		return $this->getData('types');
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
