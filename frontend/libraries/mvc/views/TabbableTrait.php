<?php
namespace themes\clipone\views;

use packages\base\{Exception, View};
use themes\clipone\{events, Tab};

trait TabbableTrait {
	/**
	 * @var Tab[]
	 */
	protected $tabs = [];

	/**
	 * @var string|null
	 */
	protected $active;

	public function addTab(Tab $tab) {
		$tab->setParent($this);
		$this->tabs[$tab->getName()] = $tab;
	}

	public function getTab(string $tab): ?Tab {
		return $this->tabs[$tab] ?? null;
	}

	public function getTabs(): array {
		return array_values($this->tabs);
	}
	
	/**
	 * @param string $tab
	 * @throws Exception if could not find a tab with given name
	 * @return void
	 */
	public function activeTab(string $tab): void {
		if (!isset($this->tabs[$tab])) {
			throw new Exception("cannot find tab");
		}
		$this->active = $tab;
	}

	public function getActiveTab(): ?Tab {
		if (!$this->tabs) {
			return null;
		}
		if (!$this->active) {
			$this->active = array_keys($this->tabs)[0];
		}
		return $this->tabs[$this->active];
	}

	public function buildTabs(): string {
		$html = "";
		$activeTab = $this->getActiveTab();
		if ($activeTab) {
			$active = $activeTab->getName();
		}
		foreach ($this->tabs as $tab) {
			$html .= "<li id=\"tab-" . $tab->getName() . "\"";
			if ($tab->getName() == $active) {
				$html .= " class=\"active\"";
			}
			$html .= "><a href=\"" . $tab->getLink() . "\">" . $tab->getTitle() . "</a></li>";
		}
		return $html;
	}


	public function triggerTabs() {
		$event = new events\InitTabsEvent($this);
		$event->trigger();
	}
	public function __beforeLoad(){
		$tab = $this->getActiveTab();
		if ($tab) {
			$view = $tab->getView();
			$view->isActiveTab(true);
			(new View\events\BeforeLoad($view))->trigger();
			$view->dynamicData()->trigger();
			if (method_exists($view, "__beforeLoad")) {
				$view->__beforeLoad();
			}
			(new View\events\AfterLoad($view))->trigger();
			
			foreach (['title', 'description', 'errors', 'bodyClasses', 'fixedHeader'] as $key) {
				$this->$key = $view->$key;
			}
		}
	}

	public function __call($name, $arguments) {
		$tab = $this->getActiveTab();
		if ($tab) {
			$view = $tab->getView();
			return $view->$name(...$arguments);
		}
	}

	public function __get($name) {
		$tab = $this->getActiveTab();
		if ($tab) {
			$view = $tab->getView();
			return $view->$name ?? null;
		}
	}
	public function __set($name, $value) {
		$tab = $this->getActiveTab();
		if ($tab) {
			$view = $tab->getView();
			$view->$name = $value;
		}
	}
	public function setData($data, ?string $key = null): void {
		$tab = $this->getActiveTab();
		if ($tab) {
			$this->___call(__FUNCTION__, func_get_args());
		} else {
			parent::setData($data, $key);
		}
	}
	public function export() {
		$tab = $this->getActiveTab();
		if ($tab) {
			$view = $tab->getView();
			if (method_exists($view, "export")) {
				return call_user_func_array([$view, "export"], $arguments);

			}
		}
	}
	public function getData(?string $key = null) {
		$tab = $this->getActiveTab();
		if ($tab) {
			return $this->___call(__FUNCTION__, func_get_args());
		}
		return parent::getData($key);
	}
	public function addError(View\Error $error): void {
		$this->___call(__FUNCTION__, func_get_args());
	}
	public function getError() {
		return $this->___call(__FUNCTION__, func_get_args());
	}
	public function getErrors(): array {
		return $this->___call(__FUNCTION__, func_get_args());
	}
	public function __getData(?string $key = null) { 
		return parent::getData($key);
	}
	public function __setData($data, ?string $key = null): void {
		parent::setData($data, $key);
	}
	public function ___call($method, $arguments) {
		$tab = $this->getActiveTab();
		if ($tab) {
			$view = $tab->getView();
			return call_user_func_array([$view, $method], $arguments);
		}
	}
}