<?php
namespace packages\userpanel\views\logs;
use \packages\userpanel\{log, views\form};
class delete extends form{
	public function setLog(log $log){
		$this->setData($log, 'log');
	}
	protected function getLog():log{
		return $this->getData("log");
	}
}
