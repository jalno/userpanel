<?php
namespace themes\clipone\views\users;
use \packages\userpanel\views\users\edit as usersEditView;
use \packages\userpanel;
use \packages\userpanel\usertype;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
use \packages\base\translator;
use \packages\base\frontend\theme;
class edit extends usersEditView{
	use viewTrait,formTrait;
	protected $usertypes = array();
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('users'),
			translator::trans('user.edit'),
			$this->getDataForm('name')
		));

		$this->addAssets();
		$this->setNavigation();
	}
	private function addAssets(){
		$this->addJSFile(theme::url('assets/plugins/jquery-validation/dist/jquery.validate.min.js'));
		$this->addJSFile(theme::url('assets/js/users.edit.js'));
	}
	private function setNavigation(){
		$item = new menuItem("users");
		$item->setTitle(translator::trans('users'));
		$item->setURL(userpanel\url('users'));
		$item->setIcon('clip-users');
		breadcrumb::addItem($item);

		$item = new menuItem("user");
		$item->setTitle($this->getDataForm('name'));
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
}
