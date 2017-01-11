<?php
namespace packages\userpanel\views\profile;
use \packages\userpanel\user\socialnetwork;
use \packages\userpanel\views\form;
use \packages\userpanel\authorization;
class edit extends form{
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
		$this->setDataForm($user->toArray());
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
