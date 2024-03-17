<?php

namespace themes\clipone\views;

use packages\base\Options;
use packages\userpanel\{Country, views\register as registerView};
use packages\userpanel\Register\RegisterField;
use packages\userpanel\Register\RegisterFields;
use themes\clipone\{viewTrait, views\formTrait, views\CountryCodeToReigonCodeTrait};

class register extends RegisterView
{
	use ViewTrait, FormTrait, CountryCodeToReigonCodeTrait;

	public function __beforeLoad()
	{
		$this->setTitle(t("register"));
		$this->addBodyClass('register');
		$this->initFormData();
		$this->dynamicDataBuilder();
	}

	public function getFields(): array
	{
		$totalCount = count($this->getRegisterFields());
		$credientialsCount = count(RegisterFields::credientials());
		$haveMoreThanOneField = $totalCount - $credientialsCount > 1;

		return array_map(
			function (RegisterField $field) use ($haveMoreThanOneField): array {
				switch ($field) {
					case RegisterField::NAME:
					case RegisterField::LASTNAME:
					case RegisterField::ADDRESS:
					case RegisterField::CITY:
					case RegisterField::ZIP:
						return array(
							'classes' => $haveMoreThanOneField ? 'col-md-6' : 'col-md-12',
							'field' => array(
								'name' => $field->value,
								'required' => $field->isRequired(),
								'placeholder' => t("register.user.{$field->value}"),
							),
						);
						break;
					case RegisterField::COUNTRY:
						return array(
							'classes' => $haveMoreThanOneField ? 'col-md-6' : 'col-md-12',
							'field' => array(
								'type' => 'select',
								'name' => $field->value,
								'required' => $field->isRequired(),
								'placeholder' => t("register.user.{$field->value}"),
								'options' => $this->getCountriesForSelect(),
							),
						);
						break;
					case RegisterField::PHONE:
					case RegisterField::CELLPHONE:
						return array(
							'classes' => 'col-md-12',
							'field' => array(
								'name' => "{$field->value}[number]",
								'required' => $field->isRequired(),
								'placeholder' => t("register.user.{$field->value}"),
								'input-group' => array(
									'first' => array(
										array(
											'type' => 'select',
											'name' => "{$field->value}[code]",
											'options' => array(),
										),
									),
								),
							),
						);
						break;
					case RegisterField::EMAIL:
						return array(
							'classes' => 'col-md-12',
							'field' => array(
								'name' => $field->value,
								'required' => $field->isRequired(),
								'type' => 'email',
								'icon' => 'fa fa-envelope',
								'placeholder' => t("register.user.{$field->value}"),
							),
						);
						break;
				}
			},
			$this->getRegisterFields()
		);
	}

	protected function getTOSUrl(): ?string
	{
		return Options::get("packages.userpanel.tos_url");
	}

	protected function getCountriesForSelect(): array
	{
		return array_map(function ($country) {
			return array(
				'title' => $country->name,
				'value' => $country->id
			);
		}, $this->getCountries());
	}
	private function dynamicDataBuilder()
	{
		$dd = $this->dynamicData();

		$dd->setData("countriesCode", $this->generateCountiesArray());
		$dd->setData("defaultCountryCode", $this->getDefaultCountryCode());
	}
	private function initFormData(): void
	{
		if (!$this->getDataForm('country')) {
			$country = Country::getDefaultCountry();
			$this->setDataForm($country->id, 'country');
		}
	}
}
