<?php
namespace packages\userpanel\views\logs;
use \packages\base\views\traits\form as formTrait;
use \packages\userpanel\{authorization, user, log, views\listview};
class search extends listview{
	use formTrait;
	protected $canView;
	protected $canDelete;
	static protected $navigation;
	function __construct(){
		$this->canView = authorization::is_accessed('logs_view');
		$this->canDelete = authorization::is_accessed('logs_delete');
	}
	static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('logs_search');
	}
	protected function getLogs():array{
		return $this->getDataList();
	}
}
