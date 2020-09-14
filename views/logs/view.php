<?php
namespace packages\userpanel\views\logs;

use packages\userpanel\{Log, View as UserpanelView};

class View extends UserpanelView {
	public function setLog(Log $log): void {
		$this->setData($log, 'log');
	}
	protected function getLog(): Log {
		return $this->getData("log");
	}
}
