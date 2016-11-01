<?php
namespace themes\clipone\views\tools;
use \packages\userpanel\views\tools\usertype as usertype_list;
use \packages\userpanel;
use \packages\userpanel\user;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \packages\base\translator;
use \packages\base\frontend\theme;

class usertype extends usertype_list{
	use viewTrait, listTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('tools'),
			translator::trans('usertype'),
			translator::trans('list')
		));
		$this->setButtons();
		navigation::active("tools/usertype");
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
			$item = new menuItem("tools");
			$item->setTitle(translator::trans('tools'));
			$item->setIcon('clip-users');
			$item->addItem($usertype);
			navigation::addItem($item);
		}
	}
	protected function getPermissionsLink($count, $id){
		if($count > 0){
			return "<a href=\"".userpanel\url('tools/permissions/'.$id)."\"><span class=\"badge\">".$count."</span></a>";
		}else{
			return "<span class=\"badge\">".$count."</span>";
		}
	}
	protected function getPriorityLink($count, $id){
		if($count > 0){
			return "<a href=\"".userpanel\url('tools/priorit/'.$id)."\"><span class=\"badge\">".$count."</span></a>";
		}else{
			return "<span class=\"badge\">".$count."</span>";
		}
	}
	protected function getOptionsLink($count, $id){
		if($count > 0){
			return "<a href=\"".userpanel\url('tools/options/'.$id)."\"><span class=\"badge\">".$count."</span></a>";
		}else{
			return "<span class=\"badge\">".$count."</span>";
		}
	}
}
