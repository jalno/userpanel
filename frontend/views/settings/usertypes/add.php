<?php
namespace themes\clipone\views\settings\usertypes;
use \packages\base\translator;
use \packages\userpanel\views\settings\usertypes\add as usertypeEdit;
use \themes\clipone\navigation;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
class add extends usertypeEdit{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('settings'),
			translator::trans('usertype.add')
		));
		navigation::active("settings/usertypes");
	}
	protected function translatePermission($permission){
		$trans = translator::trans('usertype.permissions.'.$permission);
		return($trans ? $trans : $permission);
	}
}
