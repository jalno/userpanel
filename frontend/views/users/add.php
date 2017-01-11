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
}
