<?php
namespace packages\userpanel\views;

use packages\userpanel\{Country};
use packages\userpanel\Register\RegisterField;

class Register extends Form {
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

	public function setRegisterFields(array $values): void
	{
		$this->setData($values, 'register_fields');
	}

	/**
	 * @return RegisterField[]
	 */
	public function getRegisterFields(): array
	{
		return $this->getData('register_fields') ?? [];
	}
}
