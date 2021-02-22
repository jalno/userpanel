<?php
namespace themes\clipone\views\users;

use packages\base\{Options};
use packages\userpanel\{Country, views\users\Add as UsersAddView, User};
use themes\clipone\{Breadcrumb, Navigation, navigation\MenuItem, ViewTrait, views\FormTrait, views\CountryCodeToReigonCodeTrait};
use function packages\userpanel\url;

class Add extends UsersAddView {
	use CountryCodeToReigonCodeTrait, ViewTrait, FormTrait;

	public function __beforeLoad() {
		$this->setTitle(t('user.add'));
		$this->addBodyClass('users');
		$this->addBodyClass('users_add');
		$this->setNavigation();
		$this->initFormData();
		$this->dynamicDataBuilder();
	}
	protected function getCountriesForSelect(): array {
		return array_map(function($country) {
			return array(
				'title' => $country->name,
				'value' => $country->id,
			);
		}, $this->getCountries());
	}
	protected function getTypesForSelect(): array {
		return array_map(function($type) {
			return array(
				'title' => $type->title,
				'value' => $type->id,
			);
		}, $this->getTypes());
	}
	protected function getInputGroupArrayFor(string $field): array {
		$inputGroup = array(
			'left' => array(),
			'right' => array(),
		);
		$privacy = $this->getFieldPrivacyGroupBtn($field);
		if ($privacy) {
			$inputGroup['last'][] = $privacy;
		}
		if (in_array($field, ['phone', 'cellphone'])) {
			$inputGroup['first'][] = array(
				'type' => 'select',
				'name' => "{$field}[code]",
				'options' => array(),
			);
		}
		return $inputGroup;
	}
	protected function getFieldPrivacyGroupBtn(string $field): ?array {
		$privacy = true;
		return $this->canEditPrivacy ? array(
			'type' => 'button',
			'icon' => $privacy ? 'fa fa-eye' : 'fa fa-eye-slash',
			'text' => t('user.edit.privacy.'.($privacy ? 'public' : 'private')),
			'class' => array('btn','btn-default'),
			'dropdown' => array(
				array(
					'icon' => 'fa fa-eye',
					'link' => '#',
					'class' => array('changevisibity'),
					'data' => array(
						'field' => $field,
						'visibility' => 'public'
					),
					'title' => t('user.edit.privacy.public')
				),
				array(
					'icon' => 'fa fa-eye-slash',
					'link' => '#',
					'class' => array('changevisibity'),
					'data' => array(
						'field' => $field,
						'visibility' => 'private'
					),
					'title' => t('user.edit.privacy.private')
				),
			),
		) : null;
	}
	private function dynamicDataBuilder() {
		$dd = $this->dynamicData();
		$dd->setData("countriesCode", $this->generateCountiesArray());
		$dd->setData("defaultCountryCode", $this->getDefaultCountryCode());
	}
	private function setNavigation(): void {
		$item = new MenuItem("users");
		$item->setTitle(t('users'));
		$item->setURL(url('users'));
		$item->setIcon('clip-users');
		BreadCrumb::addItem($item);
	
		$item = new MenuItem("add");
		$item->setTitle(t('user.add'));
		$item->setIcon('clip-user-plus');
		BreadCrumb::addItem($item);

		Navigation::active("users/list");
	}
	private function initFormData(): void {
		if (!$this->getDataForm("country")) {
			$country = Country::getDefaultCountry();
			$this->setDataForm($country->id, "country");
		}
		if (!$this->getDataForm("type")) {
			$options = Options::get('packages.userpanel.register');
			if (isset($options['type'])) {
				$this->setDataForm($options['type'], 'type');
			}
		}
		if (!$this->getDataForm("credit")) {
			$this->setDataForm(0, "credit");
		}
		if (!$this->getDataForm("status")) {
			$this->setDataForm(User::active, "status");
		}
	}
}
