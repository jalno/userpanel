<?php
namespace themes\clipone\views\logs;

use packages\userpanel;
use packages\userpanel\{user, views\logs\search as logsSearch, authorization};
use themes\clipone\{navigation, navigation\menuItem, ViewTrait, views\ListTrait, views\FormTrait};
use packages\base\translator;

class search extends logsSearch {

	use ViewTrait, FormTrait, ListTrait;

	protected $multiuser;
	public function __beforeLoad(){
		$this->setTitle(translator::trans('users.logs'));
		$this->setButtons();
		navigation::active("logs");
		$this->multiuser = (bool)authorization::childrenTypes();
		$this->addBodyClass("users-logs");
		$this->setFormData();
	}
	private function setFormData(){
		$userID = $this->getDataForm("user");
		if ($userID) {
			$user = (new User)->byID($userID);
			if ($user) {
				$this->setDataForm($user->getFullName(), "user_name");
			}
		}
	}
	public function setButtons(){
		$this->setButton('view', $this->canView, [
			'title' => translator::trans('logs.view'),
			'icon' => 'fa fa-credit-card',
			'classes' => ['btn', 'btn-xs', 'btn-green']
		]);
		$this->setButton('delete', $this->canDelete, [
			'title' => translator::trans('logs.delete'),
			'icon' => 'fa fa-times',
			'classes' => ['btn', 'btn-xs', 'btn-bricky']
		]);
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$item = new menuItem("logs");
			$item->setTitle(translator::trans("users.logs"));
			$item->setURL(userpanel\url('logs/search'));
			$item->setIcon('fa fa-user-secret');
			navigation::addItem($item);
		}
	}
	public function getComparisonsForSelect(){
		return [
			[
				'title' => translator::trans('search.comparison.contains'),
				'value' => 'contains'
			],
			[
				'title' => translator::trans('search.comparison.equals'),
				'value' => 'equals'
			],
			[
				'title' => translator::trans('search.comparison.startswith'),
				'value' => 'startswith'
			]
		];
	}

	/**
	 * Export logs to ajax or api requests.
	 */
	public function export(): array {

		return array(
			"data" => array_merge(array(
				"permissions" => array(
					'canView' => $this->canView,
					'canDelete' => $this->canDelete,
				),
				"items" => array_map(function($log) {
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
				}, $this->getDataList()),
			), $this->getCursorExportData()),
		);
	}
}
