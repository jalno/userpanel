<?php
namespace themes\clipone\views\settings\apis;
use packages\userpanel;
use themes\clipone\{navigation, navigation\menuItem, viewTrait, views\listTrait, views\formTrait};
use packages\userpanel\{views\settings\apis\Search as parentView, authorization, authentication, user\Api};

class search extends parentView {
	use viewTrait, listTrait, formTrait;
	public static function onSourceLoad() {
		parent::onSourceLoad();
		if(parent::$navigation) {
			if($settings = navigation::getByName("settings")){
				$item = new menuItem("apis");
				$item->setTitle(t("userpanel.apis"));
				$item->setURL(userpanel\url("settings/apis"));
				$item->setIcon("fa fa-share-alt-square");
				$settings->addItem($item);
			}
		}
	}
	protected $multiuser;
	public function __beforeLoad() {
		$this->multiuser = (bool) authorization::childrenTypes();
		$this->setTitle(t("userpanel.apis"));
		$this->addBodyClass("userpanel-apis");
		$this->setButtons();
		$this->setFormData();
		navigation::active("settings/apis");
	}
	private function setFormData() {
		if (!$this->multiuser or !$this->canAdd) {
			return;
		}
		if ($user = $this->getDataForm("user")) {
			if ($user = user::byId($user)) {
				$this->setDataForm($user->getFullName(), "user_name");
			}
		} else {
			$this->setDataForm(authentication::getID(), "user");
			$this->setDataForm(authentication::getUser()->getFullName(), "user_name");
		}
	}
	public function setButtons() {
		$this->setButton("apis_edit", $this->canEdit, array(
			"title" => t("userpanel.edit"),
			"icon" => "fa fa-edit",
			"classes" => array("btn", "btn-xs", "btn-teal"),
			"data" => array(
				"action" => "edit",
			),
		));
		$this->setButton("apis_delete", $this->canDelete, array(
			"title" => t("userpanel.delete"),
			"icon" => "fa fa-times",
			"classes" => array("btn", "btn-xs", "btn-bricky"),
			"data" => array(
				"action" => "delete",
			),
		));
	}
	protected function getAppsForSelect(): array {
		$apss = array();
		foreach ($this->getApps() as $app) {
			$apss[] = array(
				"title" => $app->id . "-" . $app->name,
				"value" => $app->id,
			);
		}
		return $apss;
	}
	protected function getStatusForSelect(): array {
		return array(
			array(
				"title" => t("userpanel.api.status.active"),
				"value" => Api::active,
			),
			array(
				"title" => t("userpanel.api.status.disable"),
				"value" => Api::disable,
			),
		);
	}
	protected function getComparisonsForSelect() {
		return array(
			array(
				"title" => t("search.comparison.contains"),
				"value" => "contains"
			),
			array(
				"title" => t("search.comparison.equals"),
				"value" => "equals"
			),
			array(
				"title" => t("search.comparison.startswith"),
				"value" => "startswith"
			)
		);
	}
}
