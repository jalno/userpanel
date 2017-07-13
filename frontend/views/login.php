<?php
namespace themes\clipone\views;
use \packages\base\options;
use \packages\base\frontend\theme;
use \packages\userpanel\views\login as loginView;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
class login extends loginView{
	use viewTrait, formTrait;
	protected $registerEnable = false;
	function __beforeLoad(){
		$this->setTitle("ورود");
		$registerOption = options::load('packages.userpanel.register');
		$this->registerEnable = $registerOption['enable'];
	}
}
