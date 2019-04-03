<?php
namespace packages\userpanel\views\settings\apis;
use packages\base\views\traits\form as formTrait;
use packages\userpanel\{authorization, views\listview};

class Search extends listview {
	use formTrait;
	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed("apis_search");
	}
	protected static $navigation;
	protected $canAdd;
	protected $canEdit;
	protected $canDelete;
	public function __construct(){
		$this->canAdd = authorization::is_accessed("apis_add");
		$this->canEdit = authorization::is_accessed("apis_edit");
		$this->canDelete = authorization::is_accessed("apis_delete");
	}
	public function setApps(array $apps) {
		$this->setData($apps, "apps");
	}
	protected function getApis(): array {
		return $this->getDataList();
	}
	protected function getApps(): array {
		return $this->getData("apps");
	}
}
