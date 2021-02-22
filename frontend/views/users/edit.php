<?php
namespace themes\clipone\views\users;

use packages\base\{translator, packages, frontend\theme, options};
use packages\userpanel;
use packages\userpanel\{Authentication, Country, views\users\Edit as UsersEditView, usertype, Usertype\Permission, Usertype\Permissions};
use themes\clipone\{breadcrumb, navigation, navigation\menuItem, viewTrait};
use themes\clipone\views\{CountryCodeToReigonCodeTrait, FormTrait, TabTrait};

class Edit extends UsersEditView {
	use CountryCodeToReigonCodeTrait, ViewTrait, FormTrait, TabTrait;

	protected $usertypes = array();
	private $user;

	public function __beforeLoad(): void {
		$this->user = $this->getData("user");
		$this->setTitle(t("profile.edit"));
		$this->addBodyClass('users');
		$this->addBodyClass('users_edit');
		$this->setNavigation();
		$this->dynamicDataBuilder();
	}
	protected function getCountriesForSelect(): array {
		return array_map(function($country) {
			return array(
				'title' => $country->name,
				'value' => $country->id
			);
		}, $this->getCountries());
	}
	protected function getTypesForSelect(): array {
		return array_map(function($type) {
			return array(
				'title' => $type->title,
				'value' => $type->id
			);
		}, $this->getTypes());
	}
	protected function dynamicDataBuilder(): void {
		$dd = $this->dynamicData();
		$dd->setData("userPermissions", $this->buildPermissionsArray());

		$dd->setData("countriesCode", $this->generateCountiesArray());
		$dd->setData("defaultCountryCode", $this->getDefaultCountryCode());
	}
	protected function getAvatarURL(): string {
		if ($this->user->avatar) {
			return Packages::package('userpanel')->url($this->user->avatar);
		} else {
			return Theme::url('assets/images/defaultavatar.jpg');
		}
	}
	protected function buildPermissionsArray(bool $withTranslate = false): array {
		$existentPermissions = Permissions::existentForUser(Authentication::getUser());
		$userPermissions = $this->user->getPermissions();
		return array_map(function (string $permission) use ($userPermissions, $withTranslate) {
			$item = array(
				"key" => $permission,
				"value" => in_array($permission, $userPermissions),
			);
			if ($withTranslate) {
				$item["title"] = t("usertype.permissions.{$permission}");
			}
			return $item;
		}, $existentPermissions);
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
				'value' => 98,
				'options' => array(),
			);
		}
		return $inputGroup;
	}
	protected function getFieldPrivacyGroupBtn(string $field): ?array {
		$privacy = $this->getDataForm('visibility_'.$field);
		return $this->canEditPrivacy ? array(
			'type' => 'button',
			'icon' => $privacy ? 'fa fa-eye' : 'fa fa-eye-slash',
			'text' => translator::trans('user.edit.privacy.'.($privacy ? 'public' : 'private')),
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
					'title' => translator::trans('user.edit.privacy.public')
				),
				array(
					'icon' => 'fa fa-eye-slash',
					'link' => '#',
					'class' => array('changevisibity'),
					'data' => array(
						'field' => $field,
						'visibility' => 'private'
					),
					'title' => translator::trans('user.edit.privacy.private')
				),
			),
		) : null;
	}
	protected function getUserCurrency(): string {
		if (packages::package("financial")) {
			return \packages\financial\currency::getDefault($this->user)->title;
		} else {
			return options::get("packages.userpanel.users.credit.currency.title");
		}
	}
	private function setNavigation(): void {
		$item = new menuItem("users");
		$item->setTitle(translator::trans('users'));
		$item->setURL(userpanel\url('users'));
		$item->setIcon('clip-users');
		breadcrumb::addItem($item);

		$item = new menuItem("user");
		$item->setTitle($this->getData('user')->getFullName());
		$item->setURL(userpanel\url('users/view/'.$this->getDataForm('id')));
		$item->setIcon('clip-user');
		breadcrumb::addItem($item);

		$item = new menuItem("edit");
		$item->setTitle(translator::trans('user.edit'));
		$item->setURL(userpanel\url('users/edit/'.$this->getDataForm('id')));
		$item->setIcon('clip-edit');
		breadcrumb::addItem($item);

		navigation::active("users/list");
	}
}
