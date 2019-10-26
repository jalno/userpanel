<?php
namespace themes\clipone\views\settings\usertypes;
use \packages\userpanel\views\settings\usertypes\delete as usertypesDelete;
use \packages\userpanel;
use \packages\userpanel\usertype;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
use \packages\base\translator;
class delete extends usertypesDelete{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$usertype = $this->getUserType();
		$this->setTitle(t("usertype.delete"));
		$this->setNavigation();
	}
	private function setNavigation(){
		$item = new menuItem("settings");
		$item->setTitle(translator::trans('settings'));
		$item->setURL(userpanel\url('settings'));
		$item->setIcon('clip-settings');
		breadcrumb::addItem($item);

		$item = new menuItem("usertypes");
		$item->setTitle(translator::trans('usertypes'));
		$item->setURL(userpanel\url('settings/usertypes'));
		$item->setIcon('fa fa-group');
		breadcrumb::addItem($item);

		$usertype = $this->getUserType();
		$item = new menuItem("delete");
		$item->setTitle(translator::trans('usertype.delete.byTitle', array('title' => $usertype->title)));
		$item->setURL(userpanel\url('settings/usertypes/delete/'.$usertype->id));
		$item->setIcon('fa fa-trash');
		breadcrumb::addItem($item);

		navigation::active("settings/usertypes");
	}
}
