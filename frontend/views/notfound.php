<?php
namespace themes\clipone\views;
use \packages\userpanel\views\notfound as notfoundView;
use \packages\base;
use \packages\base\translator;

use \packages\userpanel\authentication;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
class notfound extends notfoundView{
	use viewTrait;
	protected $loged_in;
	function __beforeLoad(){
		$this->setTitle(translator::trans('notfound'));
		$this->loged_in = authentication::check();

		if(!$this->loged_in){
			$this->addBodyClass('error-full-page');
		}
	}
}
