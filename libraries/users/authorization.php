<?php
namespace packages\userpanel;
use \packages\base\options;
use \packages\base\response;
use \packages\userpanel\views\ErrorView;
class authorization{
	static function is_accessed($permission, $prefix = 'userpanel'){
		if($prefix)$prefix .= '_';
		$user = authentication::getUser();
		if ($user) {
			return $user->can($prefix.$permission);
		} else {
			$guestTypeID = Options::get('packages.userpanel.usertypes.guest');
			if ($guestTypeID) {
				$type = (new Usertype)->byId($guestTypeID);
				if ($type) {
					return $type->hasPermission($prefix.$permission);
				}
			}
		}
		return false;
	}
	static function haveOrFail($permission, $prefix = 'userpanel'){
		if(!self::is_accessed($permission, $prefix)){
			throw new AuthorizationException($prefix.$permission);
		}
	}
	static function childrenTypes(){
		return authentication::getUser()->childrenTypes();
	}
}
