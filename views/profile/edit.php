<?php
namespace packages\userpanel\views\profile;

use packages\base\{Validator\CellphoneValidator};
use packages\userpanel\user\socialnetwork;
use packages\userpanel\views\form;
use packages\userpanel\authorization;


class edit extends form{
	use settingsTrait;
	protected $canEditPrivacy;
	function __construct(){
		$this->canEditPrivacy = authorization::is_accessed('profile_edit_privacy');
	}
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
		$userArray = $user->toArray();
		$defaultCountryCode = CellphoneValidator::getDefaultCountryCode();
		foreach (array("phone", "cellphone") as $field) {
			if (strpos($userArray[$field], ".")) {
				$item = explode(".", $userArray[$field]);
				$userArray["{$field}[code]"] = $item[0] ?: $defaultCountryCode;
				$userArray["{$field}[number]"] = $item[1];
			} else {
				$userArray["{$field}[code]"] = $defaultCountryCode;
				$userArray["{$field}[number]"] = $userArray[$field];
			}
		}
		$this->setDataForm($userArray);
		foreach($user->socialnetworks as $socialnet){
			$this->setDataForm($socialnet->username, 'socialnets['.$socialnet->network.']');
		}
		foreach(array(
			'email',
			'cellphone',
			'phone',
			'socialnetworks_'.socialnetwork::telegram,
			'socialnetworks_'.socialnetwork::instagram,
			'socialnetworks_'.socialnetwork::skype,
			'socialnetworks_'.socialnetwork::twitter,
			'socialnetworks_'.socialnetwork::facebook,
			'socialnetworks_'.socialnetwork::gplus,
		) as $field){
			$this->setDataForm($user->getVisibility($field), 'visibility_'.$field);
		}
	}
}
