<?php
namespace packages\userpanel\views\logs;

use packages\base\views\traits\form as FormTrait;
use packages\userpanel\{Authorization, Log, views\Listview};

class Search extends Listview {
	/** @var bool whether logs item shall appear on navigation menu or not*/
	protected static $navigation;

	public static function onSourceLoad(){
		self::$navigation = Authorization::is_accessed('logs_search');
	}

	use FormTrait;

	/** @var bool whether userpanel_logs_view permission is granted */
	protected $canView;

	/** @var bool whether userpanel_logs_delete permission is granted */
	protected $canDelete;

	/** @var bool whether logs_search_system_logs permission is granted */
	protected $hasAccessToSystemLogs;

	public function __construct(){
		$this->canView = Authorization::is_accessed('logs_view');
		$this->canDelete = Authorization::is_accessed('logs_delete');
		$this->hasAccessToSystemLogs = Authorization::is_accessed('logs_search_system_logs');
	}
	protected function getLogs():array{
		return $this->getDataList();
	}
}
