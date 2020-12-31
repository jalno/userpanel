<?php
namespace packages\userpanel\usertype;

use packages\base\events;
use packages\userpanel\{events\usertype_permissions_list, User};

class Permissions {
	static protected $permissions = array();
	static public function add($permission){
		if(!in_array($permission, self::$permissions)){
			self::$permissions[] = $permission;
		}
	}
	static public function get(){
		events::trigger(new usertype_permissions_list());
		return self::$permissions;
	}
	static public function has($permission){
		return in_array($permission, self::$permissions);
	}
	public static function existentForUser(User $user, bool $withoutDisabled = true): array {
		$allPermissions = $user->isManager() ? static::get() : $user->getPermissions();
		if ($withoutDisabled) {
			$disabledPermissions = Permission::getDisabledPermissions();
			if ($disabledPermissions) {
				$allPermissions = array_diff($allPermissions, $disabledPermissions);
			}
		}
		return array_values($allPermissions);
	}
}
