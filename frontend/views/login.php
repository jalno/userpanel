<?php
namespace themes\clipone\views;
use \packages\userpanel\views\login as loginView;
use \packages\base\options;
class login extends loginView{
	protected $registerEnable = false;
	function __construct(){
		$this->setTitle("ورود");
		$registerOption = options::load('packages.userpanel.register');
		$this->registerEnable = $registerOption['enable'];
	}
}
