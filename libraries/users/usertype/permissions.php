<?php
namespace packages\userpanel\usertype;

use packages\base\{events, Options};
use packages\userpanel\{events\usertype_permissions_list, User};

class Permissions {

	static protected $permissions = array();
	private static $disabledPermissions = null;

	public static function add($permission) {
		if(!in_array($permission, self::$permissions)){
			self::$permissions[] = $permission;
		}
	}

	public static function get(){
		events::trigger(new usertype_permissions_list());
		return self::$permissions;
	}

	public static function has($permission){
		return in_array($permission, self::$permissions);
	}

	public static function existentForUser(User $user, bool $withoutDisabled = true): array {
		$allPermissions = $user->isManager() ? static::get() : $user->getPermissions();
		if ($withoutDisabled) {
			$disabledPermissions = self::getDisabledPermissions();
			if ($disabledPermissions) {
				$allPermissions = array_diff($allPermissions, $disabledPermissions);
			}
		}
		return array_values($allPermissions);
	}

	public static function getDisabledPermissions(bool $useCache = true): array {
		if (self::$disabledPermissions === null or !$useCache) {
			self::$disabledPermissions = Options::get("packages.userpanel.disabledpermisions");
			if (!self::$disabledPermissions or !is_array(self::$disabledPermissions)) {
				self::$disabledPermissions = array();
			}
		}
		return self::$disabledPermissions;
	}

}
