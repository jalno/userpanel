<?php
namespace packages\userpanel;
class AuthorizationException extends \Exception{
	protected $permission;
	public function __construct(string $permission){
		parent::__construct("this user is not authorized for ".$permission);
		$this->permission = $permission;
	}
	public function getPermission():string{
		return $this->permission;
	}
}