<?php
namespace themes\clipone\views;

use packages\base\View;

trait TabTrait {
	protected $activeTab = false;

	/**
	 * Getter and setter for activeTab property
	 * 
	 * @param bool|null $activeTab
	 * @return bool current value of activeTab
	 */
	public function isActiveTab(?bool $activeTab = null): bool {
		if ($activeTab !== null) {
			$this->activeTab = $activeTab;
		}
		return $this->activeTab;
	}

	/**
	 * Ouput the html file.
	 * 
	 * @return void
	 */
	public function output() {
		if ($this->activeTab) {
			$this->outputTab();
		} else {
			parent::output();
		}
	}

	/**
	 * Ouput the html file.
	 * 
	 * @return void
	 */
	public function outputTab() {
		$this->loadHTMLFile();
		if (!$this->file) {
			return;
		}
		require_once($this->file->getPath());
		(new View\events\AfterOutput($this))->trigger();
	}

	public function __get($key) {
		if (isset($this->$key)) {
			return $this->$key;
		}
	}
}