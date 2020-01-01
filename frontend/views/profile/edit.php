<?php
namespace themes\clipone\views\profile;

use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\base\packages;


use \packages\userpanel\views\profile\edit as profileEditView;
use \packages\userpanel;
use \packages\userpanel\usertype;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use themes\clipone\{viewTrait, views\formTrait, views\TabTrait};
class edit extends profileEditView{
	use viewTrait,formTrait, TabTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('profile.edit')
		));

		$this->addBodyClass('profile');
		$this->addBodyClass('profile_edit');
		$this->setNavigation();
	}
	private function setNavigation(){
		$item = new menuItem("profile");
		$item->setTitle(translator::trans('profile.view'));
		$item->setURL(userpanel\url('profile/view'));
		$item->setIcon('fa fa-user');
		breadcrumb::addItem($item);


		$editItem = new menuItem("edit");
		$editItem->setTitle(translator::trans('profile.edit'));
		$editItem->setURL(userpanel\url('profile/edit'));
		$editItem->setIcon('fa fa-edit');
		breadcrumb::addItem($editItem);

		navigation::active("dashboard");
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
}
