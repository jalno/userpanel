<?php
namespace packages\userpanel\views\users;
use \packages\base\packages;
use \packages\userpanel\authorization;
class view extends \packages\userpanel\view{
	use settingsTrait;
	protected $canEdit;
	protected $canLogin;
	function __construct(){
		$this->canEdit = authorization::is_accessed('users_edit');
		$this->canViewInvisibles = authorization::is_accessed('users_view_invisibles');
		$this->canLogin = authorization::is_accessed('users_login');
	}
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
		return authorization::is_accessed('users_settings');
	}
	public function setSettings(array $settings){
		$this->setData($settings, 'settings');
	}
	protected function getSettings():array{
		return $this->getData('settings');
	}
}
