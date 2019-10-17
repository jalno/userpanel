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
		$this->setTitle(t("register"));
		$this->addBodyClass('register');
		$this->setCounties();
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
