<?php
namespace themes\clipone\views\users;

use packages\base\{Options};
use function packages\userpanel\url;
use packages\userpanel\{views\users\add as usersAddView, User};
use themes\clipone\{breadcrumb, navigation, navigation\menuItem, viewTrait, views\formTrait};

class add extends usersAddView{
	use viewTrait, formTrait;
	public function __beforeLoad() {
		$this->setTitle(t('user.add'));
		$this->addBodyClass('users');
		$this->addBodyClass('users_add');
		$this->setNavigation();
		$this->initFormData();
	}
	private function setNavigation() {
		$item = new menuItem("users");
		$item->setTitle(t('users'));
		$item->setURL(url('users'));
		$item->setIcon('clip-users');
		breadcrumb::addItem($item);
	
		$item = new menuItem("add");
		$item->setTitle(t('user.add'));
		$item->setIcon('clip-user-plus');
		breadcrumb::addItem($item);

		navigation::active("users/list");
	}
	protected function getCountriesForSelect(){
		$options = array();
		foreach($this->getCountries() as $country){
			$options[] = array(
				'title' => $country->name,
				'value' => $country->id
			);
		}
		return $options;
	}
	protected function getTypesForSelect(){
		$options = array();
		foreach($this->getTypes() as $type){
			$options[] = array(
				'title' => $type->title,
				'value' => $type->id
			);
		}
		return $options;
	}
	protected function getFieldPrivacyGroupBtn($field){
		if(!$this->canEditPrivacy){
			return false;
		}
		$privacy = true;
		$button = array(
			'type' => 'button',
			'icon' => $privacy ? 'fa fa-eye' : 'fa fa-eye-slash',
			'text' => t('user.edit.privacy.'.($privacy ? 'public' : 'private')),
			'class' => array('btn','btn-default'),
			'dropdown' => array()
		);

		$button['dropdown'][] = array(
			'icon' => 'fa fa-eye',
			'link' => '#',
			'class' => array('changevisibity'),
			'data' => array(
				'field' => $field,
				'visibility' => 'public'
			),
			'title' => t('user.edit.privacy.public')
		);
		$button['dropdown'][] = array(
			'icon' => 'fa fa-eye-slash',
			'link' => '#',
			'class' => array('changevisibity'),
			'data' => array(
				'field' => $field,
				'visibility' => 'private'
			),
			'title' => t('user.edit.privacy.private')
		);
		return array(
			'left' => array($button)
		);
	}
	private function initFormData() {
		if (!$this->getDataForm("country")) {
			$this->setDataForm(105, "country");
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
