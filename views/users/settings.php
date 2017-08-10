<?php
namespace packages\userpanel\views\users;
use \packages\userpanel\user;
use \packages\userpanel\views\form;
class settings extends form{
	public function setUser(user $user){
		$this->setData($user, 'user');
	}
	protected function getUser():user{
		return $this->getData('user');
	}
	public function setSettings(array $settings){
		$this->setData($settings, 'settings');
		foreach($settings as $tunig){
			foreach($tunig->getDataForm() as $name => $value){
				$this->setDataForm($value, $name);
			}
		}
	}
	protected function getSettings():array{
		return $this->getData('settings');
	}
}
