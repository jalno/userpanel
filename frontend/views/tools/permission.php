<?php
namespace themes\clipone\views\tools;
use \packages\userpanel\views\tools\permission as permission_list;
use \packages\userpanel;
use \packages\userpanel\user;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \packages\base\translator;
use \packages\base\frontend\theme;

class permission extends permission_list{
	use viewTrait, listTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('tools'),
			translator::trans('permissions'),
			translator::trans('list')
		));
		$this->setButtons();
		navigation::active("tools/permissions");
	}
	public function setButtons(){
		$this->setButton('delete', $this->canDelete, array(
			'title' => translator::trans('user.delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky')
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
	protected function getTypesSelected(){
		$usertypes = array();
		foreach($this->getUserTypes() as $usertype){
			$usertypes[] = array(
				'title' => $usertype->title,
				'value' => $usertype->id
			);
		}
		return $usertypes;
	}
}
