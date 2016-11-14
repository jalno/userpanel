<?php
namespace themes\clipone\views\settings\bankaccount;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\views\settings\bankaccount\edit as account_edit;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;

class edit extends account_edit{
	use viewTrait, listTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('settings'),
			translator::trans('bankaccounts'),
			translator::trans('edit')
		));
		navigation::active("settings/bankaccounts");
	}
}
