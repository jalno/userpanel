<?php
namespace packages\userpanel;
use \packages\base\response;
use \packages\userpanel\views\ErrorView;
class authorization{
	static function is_accessed($permission){
		return authentication::getUser()->can("userpanel_".$permission);
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
