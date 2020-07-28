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
		if ($this->isTab) {
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
		if ($this->source) {
			if ($this->file) {
				if (is_string($this->file)) {
					$this->file = $this->source->getFile($this->file);
				}
			} else {
				$this->file = $this->source->getHTMLFile(get_class($this));
				if (!$this->file) {
					$reflection = new \ReflectionClass(get_class($this));
					$thisFile = $reflection->getFileName();
					$sourceHome = $this->source->getHome()->getRealPath();
					$file = substr($thisFile, strlen($sourceHome) + 1);
					$file = $this->source->getFile("html" . substr($file, strpos($file, "/")));
					if ($file->exists()) {
						$this->file = $file;
					}
				}
			}
		}
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