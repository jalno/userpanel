<?php
namespace themes\clipone\views\settings\usertypes;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\views\settings\usertypes\listview as usertype_list;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\{formTrait, dashboard};
class listview extends usertype_list{
	use viewTrait, listTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('settings'),
			translator::trans('usertypes'),
			translator::trans('list')
		));
		$this->setButtons();
		navigation::active("settings/usertypes");
	}
	public function setButtons(){
		$this->setButton('edit', $this->canEdit, array(
			'title' => translator::trans('usertype.edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-warning')
		));
		$this->setButton('delete', $this->canDelete, array(
			'title' => translator::trans('usertype.delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky')
		));
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$settings = dashboard::getSettingsMenu();
			$usertype = new menuItem("usertypes");
			$usertype->setTitle(translator::trans("usertypes"));
			$usertype->setURL(userpanel\url('settings/usertypes'));
			$usertype->setIcon('fa fa-address-card-o');
			$settings->addItem($usertype);
		}
	}
}
