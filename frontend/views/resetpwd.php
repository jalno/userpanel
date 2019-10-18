<?php
namespace themes\clipone\views;
use \packages\userpanel\views\resetpwd as resetpwdView;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
class resetpwd extends resetpwdView{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(t("userpanel.resetpwd"));
		$this->addBodyClass('login');
		$this->addBodyClass('resetpwd');
	}
	public function hasBlocked():bool{
		foreach($this->getErrors() as $error){
			if($error->getCode() == 'userpanel.resetpwd.losingChance'){
				return true;
			}
		}
		return false;
	}
}
