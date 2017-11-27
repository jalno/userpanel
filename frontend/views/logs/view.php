<?php
namespace themes\clipone\views\logs;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\views\logs\view as logsView;
use \themes\clipone\{navigation, navigation\menuItem, breadcrumb, viewTrait};

class view extends logsView{
	use viewTrait;
	protected $user;
	protected $log;
	protected $handler;
	function __beforeLoad(){
		$this->user = $this->getUser();
		$this->log = $this->getLog();
		$this->handler = $this->log->getHandler();
		$this->setTitle(translator::trans('logs.view'));
		$this->addBodyClass('logs');
		$this->addBodyClass('logs_view');
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
