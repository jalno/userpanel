<?php
namespace packages\userpanel\views\profile;
use \packages\userpanel\views\form;
class edit extends form{
	public function setCountries($countries){
		$this->setData($countries, 'countries');
	}
	public function getCountries(){
		return $this->getData('countries');
	}
}
