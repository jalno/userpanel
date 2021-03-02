<?php
namespace themes\clipone\views\profile;

use packages\base\{Packages, frontend\Theme, Translator};
use packages\userpanel\{Usertype};
use packages\userpanel\views\profile\Edit as ProfileEditView;
use themes\clipone\{Breadcrumb, Navigation, navigation\MenuItem, ViewTrait};
use themes\clipone\views\{CountryCodeToReigonCodeTrait, FormTrait, TabTrait};
use function packages\userpanel\url;

class Edit extends ProfileEditView {
	use CountryCodeToReigonCodeTrait, ViewTrait, FormTrait, TabTrait;

	protected $user;

	public function __beforeLoad() {
		$this->user = $this->getData("user");
		$this->setTitle(array(
			t('profile.edit')
		));

		$this->addBodyClass('profile');
		$this->addBodyClass('profile_edit');
		$this->setNavigation();
		$this->prepairDynamicData();
	}
	private function setNavigation(){
		$item = new menuItem("profile");
		$item->setTitle(t('profile.view'));
		$item->setURL(url('profile/view'));
		$item->setIcon('fa fa-user');
		breadcrumb::addItem($item);


		$editItem = new menuItem("edit");
		$editItem->setTitle(t('profile.edit'));
		$editItem->setURL(url('profile/edit'));
		$editItem->setIcon('fa fa-edit');
		breadcrumb::addItem($editItem);

		navigation::active("dashboard");
	}
	protected function getCountriesForSelect(): array {
		return array_map(function($country) {
			return array(
				'title' => $country->name,
				'value' => $country->id
			);
		}, $this->getCountries());
	}
	protected function getAvatarURL(): string {
		if ($this->getUserData('avatar')) {
			return packages::package('userpanel')->url($this->getUserData('avatar'));
		} else {
			return theme::url('assets/images/defaultavatar.jpg');
		}
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
				'disabled' => $field == 'cellphone',
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
	private function prepairDynamicData(): void {
		$dd = $this->dynamicData();
		$dd->setData("countriesCode", $this->generateCountiesArray());
		$dd->setData("defaultCountryCode", $this->getDefaultCountryCode());
	}
}
