<?php
namespace themes\clipone\views;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\userpanel\views\lock as lockView;
use \themes\clipone\views\formTrait;
class lock extends lockView{
	use formTrait;
	protected $user;
	function __beforeLoad(){
		$this->setTitle(translator::trans('unlock'));
		$this->user = $this->getUser();
		$this->addAssets();
	}
	private function addAssets(){
		$this->addJSFile(theme::url('assets/plugins/jquery-validation/dist/jquery.validate.min.js'));
		$this->addJSFile(theme::url('assets/js/pages/lock.js'));
	}
}
