<?php
namespace themes\clipone\views\profile;
use \packages\base\translator;
use \packages\userpanel;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use themes\clipone\{viewTrait, views\formTrait, views\TabTrait};
use \packages\userpanel\views\profile\settings as profileSettingsView;
class settings extends profileSettingsView{
	use viewTrait, formTrait, TabTrait;
	protected $user;
	function __beforeLoad(){
		$this->user = $this->getUser();
		$this->setTitle(translator::trans('profile.settings'));

		$this->addBodyClass('profile');
		$this->addBodyClass('profile-settings');
		$this->setNavigation();
	}
	private function setNavigation(){
		$item = new menuItem("profile");
		$item->setTitle(translator::trans('profile.view'));
		$item->setURL(userpanel\url('profile/view'));
		$item->setIcon('fa fa-user');
		breadcrumb::addItem($item);


		$settingsItem = new menuItem("settings");
		$settingsItem->setTitle(translator::trans('profile.settings'));
		$settingsItem->setURL(userpanel\url('profile/settings'));
		$settingsItem->setIcon('fa fa-cog fa-spin');
		breadcrumb::addItem($settingsItem);

		navigation::active("dashboard");
	}
}
