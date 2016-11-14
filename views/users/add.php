<?php
namespace packages\userpanel\views\users;
use \packages\userpanel\views\form;
use \packages\userpanel\authorization;

class add extends form{
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
}
