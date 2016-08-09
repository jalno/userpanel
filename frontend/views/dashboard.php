<?php
namespace themes\clipone\views;
use \packages\userpanel\views\dashboard as dashboardView;
use \packages\base;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
class dashboard extends dashboardView{
	function __construct(){
		$this->setTitle("پیشخوان");
		navigation::active("dashboard");
	}
	public static function onSourceLoad(){
		$item = new menuItem("dashboard");
		$item->setTitle("پیشخوان");
		$item->setURL(base\url('userpanel'));
		$item->setIcon('clip-home-3');
		navigation::addItem($item);
	}
}
