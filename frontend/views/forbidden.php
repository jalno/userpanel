<?php
namespace themes\clipone\views;
use \packages\userpanel\views\forbidden as forbiddenView;
use \packages\base;
use \packages\base\translator;

use \packages\userpanel\authentication;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
class forbidden extends forbiddenView{
	use viewTrait;
	protected $loged_in;
	function __beforeLoad(){
		$this->setTitle(translator::trans('forbidden'));
		$this->loged_in = authentication::check();

		if(!$this->loged_in){
			$this->addBodyClass('error-full-page');
		}
	}
}
