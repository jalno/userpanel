<?php
namespace packages\userpanel\views;

use packages\userpanel\{Country};

class Login extends Form {
	/**
	 * @param Country[] $countries
	 */
	public function setCountries(array $countries): void {
		$this->setData($countries, 'countries');
	}

	/**
	 * @return Country[]
	 */
	public function getCountries(): array {
		return $this->getData('countries') ?? [];
	}
}
