<?php
namespace packages\userpanel\views\users;
use \packages\userpanel\views\form;
use \packages\userpanel\authorization;

class add extends form{
	protected $canEditPrivacy;
	protected $canChangeCredit;
	function __construct(){
		$this->canEditPrivacy = authorization::is_accessed('profile_edit_privacy');
		$this->canChangeCredit = authorization::is_accessed("users_edit_credit");
	}
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
