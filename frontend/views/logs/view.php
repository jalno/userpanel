<?php
namespace themes\clipone\views\logs;

use packages\userpanel\views;
use themes\clipone\{Navigation, Breadcrumb, ViewTrait};

class View extends Views\Logs\View{
	use ViewTrait;
	protected $user;
	protected $log;
	protected $handler;
	public function __beforeLoad(){
		$this->user = $this->getUser();
		$this->log = $this->getLog();
		$this->handler = $this->log->getHandler();
		$this->setTitle(t('logs.view.title', ['log' => $this->log->id]));
		$this->addBodyClass('logs');
		$this->addBodyClass('logs_view');
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
