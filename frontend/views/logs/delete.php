<?php
namespace themes\clipone\views\logs;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\views\logs\delete as logsDelete;
use \themes\clipone\{navigation, navigation\menuItem, breadcrumb, viewTrait};

class delete extends logsDelete{
	use viewTrait;
	protected $log;
	function __beforeLoad(){
		$this->log = $this->getLog();
		$this->setTitle(translator::trans('logs.delete'));
		$this->addBodyClass('logs');
		$this->addBodyClass('logs-delete');
		$this->setNavigation();
	}
	private function setNavigation(){
		$item = new menuItem("logs");
		$item->setTitle(translator::trans('logs.view'));
		$item->setIcon('fa fa-exclamation-circle');
		breadcrumb::addItem($item);
		navigation::active("dashboard");
	}
}
