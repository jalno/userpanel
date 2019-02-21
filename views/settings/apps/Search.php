<?php
namespace packages\userpanel\views\settings\apps;
use packages\base\views\traits\form as formTrait;
use packages\userpanel\{authorization, views\listview};

class Search extends listview {
	use formTrait;
	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed("apps_search");
	}
	protected static $navigation;
	protected $canAdd;
	protected $canDelete;
	function __construct(){
		$this->canAdd = authorization::is_accessed("apps_add");
		$this->canDelete = authorization::is_accessed("apps_delete");
	}
	protected function getApps(): array {
		return $this->getDataList();
	}
}
