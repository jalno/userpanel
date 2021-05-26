<?php
namespace themes\clipone\views;

use packages\base\{Options, frontend\Theme};
use packages\userpanel\{Country, views\Login as LoginView};
use themes\clipone\{ViewTrait, views\FormTrait, views\CountryCodeToReigonCodeTrait};

class Login extends LoginView {
	use CountryCodeToReigonCodeTrait, ViewTrait, FormTrait;

	protected $registerEnable = false;

	public function __beforeLoad() {
		$this->setTitle(t("login"));
		$registerOption = Options::load('packages.userpanel.register');
		$this->registerEnable = boolval($registerOption['enable']);
		$this->dynamicDataBuilder();
		$this->addBodyClass('login-page');
	}

	private function dynamicDataBuilder() {
		$dd = $this->dynamicData();
		$dd->setData("countriesCode", $this->generateCountiesArray());
		$dd->setData("defaultCountryCode", $this->getDefaultCountryCode());
	}
}
