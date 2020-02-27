<?php
namespace themes\clipone\views;

use packages\base\Options;
use packages\userpanel\views\register as registerView;
use themes\clipone\{viewTrait, views\formTrait};

class register extends registerView{
	use viewTrait, formTrait;
	protected $countries = array();
	public function __beforeLoad(){
		$this->setTitle(t("register"));
		$this->addBodyClass('register');
		$this->setCounties();
	}
	protected function getTOSUrl(): ?string {
		return Options::get("packages.userpanel.tos_url");
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
