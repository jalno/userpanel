<?php
namespace packages\userpanel\views\tools;
use \packages\userpanel\views\listview as list_view;
use \packages\userpanel\authorization;
use \packages\base\views\traits\form as formTrait;
class permission extends list_view{
	use formTrait;
	protected $canDelete;
	protected $permissions;
	protected $usertypes;
	static protected $navigation;
	function __construct(){
		$this->canDelete = authorization::is_accessed('permission_delete');
	}
	static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('permission_list');
	}
	public function setPermissions($permissions){
		$this->permissions = $permissions;
	}
	public function getPermissions(){
		return $this->permissions;
	}
	public function setUserTypes($usertypes){
		$this->usertypes = $usertypes;
	}
	public function getUserTypes(){
		return $this->usertypes;
	}
}
