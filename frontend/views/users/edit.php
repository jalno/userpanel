<?php
namespace themes\clipone\views\users;
use packages\base\{translator, packages, frontend\theme, options};
use packages\userpanel;
use packages\userpanel\{views\users\edit as usersEditView, usertype};
use themes\clipone\{breadcrumb, navigation, navigation\menuItem, viewTrait, views\formTrait, views\TabTrait};
class edit extends usersEditView{
	use viewTrait,formTrait, TabTrait;
	protected $usertypes = array();
	private $user;
	function __beforeLoad(){
		$this->user = $this->getData("user");
		$this->setTitle(t("profile.edit"));

		
		$this->addBodyClass('users');
		$this->addBodyClass('users_edit');
		$this->setNavigation();
	}
	private function setNavigation(){
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

	protected function getAvatarURL(){
		if($this->getUserData('avatar')){
			return packages::package('userpanel')->url($this->getUserData('avatar'));
		}else{
			return theme::url('assets/images/defaultavatar.jpg');
		}
	}
	protected function getFieldPrivacyGroupBtn($field){
		if(!$this->canEditPrivacy){
			return false;
		}
		$privacy = $this->getDataForm('visibility_'.$field);
		$button = array(
			'type' => 'button',
			'icon' => $privacy ? 'fa fa-eye' : 'fa fa-eye-slash',
			'text' => translator::trans('user.edit.privacy.'.($privacy ? 'public' : 'private')),
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
			'title' => translator::trans('user.edit.privacy.public')
		);
		$button['dropdown'][] = array(
			'icon' => 'fa fa-eye-slash',
			'link' => '#',
			'class' => array('changevisibity'),
			'data' => array(
				'field' => $field,
				'visibility' => 'private'
			),
			'title' => translator::trans('user.edit.privacy.private')
		);
		return array(
			'last' => array($button)
		);
	}
	protected function getUserCurrency(): string {
		if (packages::package("financial")) {
			return \packages\financial\currency::getDefault($this->user)->title;
		} else {
			return options::get("packages.userpanel.users.credit.currency.title");
		}
	}
}
