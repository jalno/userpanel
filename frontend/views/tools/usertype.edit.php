<?php
namespace themes\clipone\views\tools;
use \packages\userpanel\views\tools\usertype_edit as permissionEdit;
use \packages\userpanel;
use \packages\userpanel\user;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \packages\base\translator;
use \packages\base\frontend\theme;

class usertype_edit extends permissionEdit{
	use viewTrait, listTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('tools'),
			translator::trans('usertype'),
			translator::trans('edit')
		));
		navigation::active("tools/usertype");
		$this->addAssets();
		$this->setButtons();
	}
	private function addAssets(){
		$this->addJSFile(theme::url('assets/js/pages/usertype.edit.js'));
	}
	public function setButtons(){
		$this->setButton('priorityedit', $this->canPriorityEdit, array(
			'title' => translator::trans('priority.edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-warning', 'tooltips', 'priorityedit'),
			'data' => array("toggle" => "modal")
		));
		$this->setButton('permissionedit', $this->canPermissionEdit, array(
			'title' => translator::trans('permission.edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-warning', 'tooltips', 'permissionedit'),
			'data' => array("toggle" => "modal")
		));
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$usertype = new menuItem("usertype");
			$usertype->setTitle(translator::trans("usertype"));
			$usertype->setURL(userpanel\url('tools/usertype'));

			$permissions = new menuItem("permissions");
			$permissions->setTitle(translator::trans("permissions"));
			$permissions->setURL(userpanel\url('tools/permissions'));

			$item = new menuItem("tools");
			$item->setTitle(translator::trans('tools'));
			$item->setIcon('clip-users');
			$item->addItem($usertype);
			$item->addItem($permissions);
			navigation::addItem($item);
		}
	}
	protected function getPermissionsSelctbox($permissions){
		$allPermission = array();
		foreach($permissions as $permission){
			$allPermission[] = array(
				"title" => $permission->title,
				"value" => $permission->id
			);
		}
		return $allPermission;
	}
	protected function getPerioritySelctbox(){
		$usertypes = array();
		foreach($this->getUserTypes() as $usertype){
			$usertypes[] = array(
				"title" => $usertype->title,
				"value" => $usertype->id
			);
		}
		return $usertypes;
	}
}
