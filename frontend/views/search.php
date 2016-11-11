<?php
namespace themes\clipone\views;
use \packages\base;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\views\search as searchView;
use \themes\clipone\viewTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation\menuItem;
class search extends searchView{
	use viewTrait, listTrait, formTrait;
	public function __beforeLoad(){
		$this->setTitle(translator::trans('search'));
		$this->setNavigation();
	}
	private function setNavigation(){
		$item = new menuItem("search");
		$item->setTitle(translator::trans('search'));
		$item->setURL(userpanel\url('search'));
		$item->setIcon('clip-search');
		breadcrumb::addItem($item);

		navigation::active("dashboard");
	}
}
