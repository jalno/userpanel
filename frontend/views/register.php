<?php
namespace themes\clipone\views;
use \packages\base\options;
use \packages\base\frontend\theme;
use \packages\userpanel\views\register as registerView;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
class register extends registerView{
	use viewTrait,formTrait;
	protected $countries = array();
	function __beforeLoad(){
		$this->setTitle("ثبت نام");
		$this->setCounties();
		$this->addAssets();
	}
	private function addAssets(){
		$this->addJSFile(theme::url('assets/plugins/jquery-validation/dist/jquery.validate.min.js'));
		$this->addJSFile(theme::url('assets/js/pages/register.js'));
	}
	private function setCounties(){
		$countries = $this->getData('countries');
		foreach($countries as $country){
			$this->countries[] = array(
				'title' => $country->name,
				'value' => $country->id
			);
		}
		if(!$this->getDataForm('country')){
			$this->setDataForm(105, 'country');
		}
	}
}
