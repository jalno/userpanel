<?php
namespace packages\userpanel;
use \packages\base\options;
use \packages\base\response;
use \packages\userpanel\views\ErrorView;
class authorization{
	static function is_accessed($permission, $prefix = 'userpanel'){
		if($prefix)$prefix .= '_';
		$user = authentication::getUser();
		if($user){
			return $user->can($prefix.$permission);
		}elseif($type = usertype::byId(options::get('packages.userpanel.usertypes.guest'))){
			return $type->hasPermission($prefix.$permission);
		}
		return false;
	}
	static function haveOrFail($permission, $prefix = 'userpanel'){
		if(!self::is_accessed($permission, $prefix)){
			throw new ErrorResponse();
		}
	}
	static function childrenTypes(){
		return authentication::getUser()->childrenTypes();
	}
	static public function FailResponse(){
		$view = new ErrorView();
		$view->setErrorCode(401);
		$view->setTitle("دسترسی غیرمجاز");
		$response = new response(false);
		$response->setView($view);
		return($response);
	}
}
