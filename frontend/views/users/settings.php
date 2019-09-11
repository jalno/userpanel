<?php
namespace themes\clipone\views\users;
use \packages\base\translator;
use \packages\userpanel;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use themes\clipone\{viewTrait, views\formTrait, views\TabTrait};
use \packages\userpanel\views\users\settings as usersSettingsView;
class settings extends usersSettingsView{
	use viewTrait, formTrait, TabTrait;
	protected $user;
	function __beforeLoad(){
		$this->user = $this->getUser();
		$this->setTitle(translator::trans('users.settings'));

		$this->addBodyClass('users');
		$this->addBodyClass('users-settings');
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

		$item = new menuItem("settings");
		$item->setTitle(translator::trans('users.settings'));
		$item->setURL(userpanel\url('users/settings/'.$this->getDataForm('id')));
		$item->setIcon('fa fa-cog fa-spin');
		breadcrumb::addItem($item);

		navigation::active("users/list");
	}
}
