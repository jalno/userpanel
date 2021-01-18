<?php
namespace themes\clipone\events;

use packages\base\{Event, View};

class InitTabsEvent extends Event {
	/**
	 * @var View
	 */
	protected $view;

	public function __construct(View $view) {
		$this->view = $view;
	}

	public function getView(): View {
		return $this->view;
	}
}