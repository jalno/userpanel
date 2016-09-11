<?php
namespace themes\clipone\views\users;
use \packages\userpanel\views\users\listview as usersListView;
use \packages\userpanel;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\views\listTrait;
use \packages\base\translator;

class listview extends usersListView{
	use listTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('users'),
			translator::trans('list')
		));
		$this->setButtons();
		navigation::active("users/list");
	}
	public function setButtons(){
		$this->setButton('view', $this->canView, array(
			'title' => translator::trans('user.profile'),
			'icon' => 'fa fa-credit-card',
			'classes' => array('btn', 'btn-xs', 'btn-green')
		));
		$this->setButton('edit', $this->canEdit, array(
			'title' => translator::trans('user.edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-teal')
		));
		$this->setButton('delete', $this->canDelete, array(
			'title' => translator::trans('user.delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky')
		));
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$item = new menuItem("users");
			$item->setTitle('کاربران');
			$item->setURL(userpanel\url('users'));
			$item->setIcon('clip-users');
			navigation::addItem($item);
		}
	}
}
