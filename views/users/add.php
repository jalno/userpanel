<?php
namespace packages\userpanel\views\users;

use packages\userpanel\{Authorization, Country, views\Form, Usertype};

class Add extends Form {

	/** @var bool */
	protected $canEditPrivacy;

	/** @var bool */
	protected $canChangeCredit;

	function __construct() {
		$this->canChangeCredit = Authorization::is_accessed('users_edit_credit');
		$this->canEditPrivacy = Authorization::is_accessed('profile_edit_privacy');
	}

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

	/**
	 * @param Usertype[] $types
	 */
	public function setTypes(array $types): void {
		$this->setData($types, 'types');
	}

	/**
	 * @return Usertype[]
	 */
	public function getTypes(): array{
		return $this->getData('types') ?? [];
	}
}
