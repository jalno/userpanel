<?php
namespace themes\clipone\views;

use packages\base\{Options, frontend\Theme};
use packages\userpanel\{Country, views\Login as LoginView};
use themes\clipone\{ViewTrait, views\FormTrait};

class Login extends LoginView {
	use ViewTrait, FormTrait;

	protected $registerEnable = false;

	public function __beforeLoad() {
		$this->setTitle(t("login"));
		$registerOption = Options::load('packages.userpanel.register');
		$this->registerEnable = boolval($registerOption['enable']);
		$this->dynamicDataBuilder();
	}

	private function dynamicDataBuilder() {
		$dd = $this->dynamicData();

		$dd->setData("countries", array_map(function($country) {
			return $country->toArray();
		}, $this->getCountries()));

		$country = Country::getDefaultCountry();
		$dd->setData("defaultCountry", $country->toArray());
	}
}
