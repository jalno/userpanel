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

	public function __construct(){
		$this->canView = Authorization::is_accessed('logs_view');
		$this->canDelete = Authorization::is_accessed('logs_delete');
	}

	/**
	 * Export logs to ajax or api requests.
	 * 
	 * @return array
	 */
	public function export() {
		$original = parent::export();
		$original['data']['permissions'] = array(
			'canView' => $this->canView,
			'canDelete' => $this->canDelete,
		);
		$original['data']['items'] = array_map(function($log) {
			$handler = $log->getHandler();
			return array(
				'id' => $log->id,
				'ip' => $log->ip,
				'time' => $log->time,
				'title' => $log->title,
				'type' => $log->type,
				'icon' => $handler->getIcon(),
				'color' => $handler->getColor(),
				'activity' => method_exists($handler, "isActivity") ? $handler->isActivity() : true,
			);
		}, $this->dataList);
		return $original;
	}
	protected function getLogs():array{
		return $this->getDataList();
	}
}
