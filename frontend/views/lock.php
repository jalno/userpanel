<?php
namespace themes\clipone\views;
use \packages\base\translator;
use \packages\userpanel\views\lock as lockView;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
class lock extends lockView{
	use viewTrait, formTrait;
	protected $user;
	function __beforeLoad(){
		$this->setTitle(translator::trans('unlock'));
		$this->user = $this->getUser();
	}
}
