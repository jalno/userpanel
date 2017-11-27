<?php
namespace packages\userpanel\views\profile;
use \packages\userpanel\authorization;
class view extends \packages\userpanel\view{
	use settingsTrait;
	public function setUserData($data){
		$this->setData($data, 'user');
	}
	public function getUserData($key){
		return($this->data['user']->$key);
	}
	public function canViewLog():bool{
		return authorization::is_accessed('logs_view');
	}
}
trait settingsTrait{
	public function canEditSettings():bool{
		return authorization::is_accessed('profile_settings');
	}
	public function setSettings(array $settings){
		$this->setData($settings, 'settings');
	}
	protected function getSettings():array{
		return $this->getData('settings');
	}
}
