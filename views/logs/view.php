<?php
namespace packages\userpanel\views\logs;
use \packages\userpanel\{authorization, user, log, view as userpanelView};
class view extends userpanelView{
	public function setUser(user $user){
		$this->setData($user, 'user');
	}
	protected function getUser():user{
		return $this->getData("user");
	}
	public function setLog(log $log){
		$this->setData($log, 'log');
	}
	protected function getLog():log{
		return $this->getData("log");
	}
}
