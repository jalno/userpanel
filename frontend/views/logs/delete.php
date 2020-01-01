<?php
namespace themes\clipone\views\logs;

use packages\userpanel\Views;
use themes\clipone\{Navigation, Breadcrumb, ViewTrait};

class delete extends Views\Logs\Delete{
	use viewTrait;
	protected $log;
	function __beforeLoad(){
		$this->log = $this->getLog();
		$this->setTitle(t('logs.delete.title', ['log' => $this->log->id]));
		$this->addBodyClass('logs');
		$this->addBodyClass('logs-delete');
		$this->setNavigation();
	}
	private function setNavigation(){
		Breadcrumb::addItem(Navigation::getByName("logs"));
		$item = new Navigation\MenuItem("log");
		$item->setTitle($this->getTitle());
		$item->setIcon('fa fa-exclamation-circle');
		Breadcrumb::addItem($item);
		Navigation::active("logs");
	}
}
