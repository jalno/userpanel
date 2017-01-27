<?php
namespace themes\clipone\views\users;
use \packages\base\http;
use \packages\userpanel\views\users\add as usersAddView;
use \packages\userpanel;
use \packages\userpanel\usertype;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
use \packages\base\translator;
use \packages\base\frontend\theme;
class add extends usersAddView{
	use viewTrait,formTrait;
	protected $usertypes = array();
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('users'),
			translator::trans('user.add')
		));
		$this->setDefaultValues();
		$this->addAssets();
		$this->setNavigation();
	}
	private function setDefaultValues(){
		if(!http::is_post()){
			$this->setDataForm(0, 'credit');
			$this->setDataForm(1, 'status');
		}
	}
	private function addAssets(){
		$this->addJSFile(theme::url('assets/plugins/jquery-validation/dist/jquery.validate.min.js'));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-inputmsg/bootstrap-inputmsg.min.js'));
		$this->addJSFile(theme::url('assets/js/pages/users.add.js'));
	}
	private function setNavigation(){
		$item = new menuItem("users");
		$item->setTitle(translator::trans('users'));
		$item->setURL(userpanel\url('users'));
		$item->setIcon('clip-users');
		breadcrumb::addItem($item);

		$item = new menuItem("add");
		$item->setTitle(translator::trans('user.add'));
		$item->setURL(userpanel\url('users/add/'));
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
			'left' => array($button)
		);
	}
}
