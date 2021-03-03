<?php
namespace themes\clipone\views;

use packages\base\Options;
use packages\userpanel\{Country, views\register as registerView};
use themes\clipone\{viewTrait, views\formTrait, views\CountryCodeToReigonCodeTrait};

class register extends RegisterView {
	use ViewTrait, FormTrait, CountryCodeToReigonCodeTrait;

	public function __beforeLoad(){
		$this->setTitle(t("register"));
		$this->addBodyClass('register');
		$this->initFormData();
		$this->dynamicDataBuilder();
	}
	protected function getTOSUrl(): ?string {
		return Options::get("packages.userpanel.tos_url");
	}
	protected function getCountriesForSelect(): array {
		return array_map(function($country) {
			return array(
				'title' => $country->name,
				'value' => $country->id
			);
		}, $this->getCountries());
	}
	private function dynamicDataBuilder() {
		$dd = $this->dynamicData();

		$dd->setData("countriesCode", $this->generateCountiesArray());
		$dd->setData("defaultCountryCode", $this->getDefaultCountryCode());
	}
	private function initFormData(): void {
		if (!$this->getDataForm('country')) {
			$country = Country::getDefaultCountry();
			$this->setDataForm($country->id, 'country');
		}
	}
}
