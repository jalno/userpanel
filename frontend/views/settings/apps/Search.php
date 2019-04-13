<?php
namespace themes\clipone\views\settings\apps;
use packages\userpanel;
use packages\userpanel\{views\settings\apps\Search as parentView, authorization, authentication};
use themes\clipone\{navigation, navigation\menuItem, viewTrait, views\listTrait, views\formTrait, views\dashboard};

class search extends parentView {
	use viewTrait, listTrait, formTrait;
	public static function onSourceLoad() {
		parent::onSourceLoad();
		if(parent::$navigation) {
			$settings = dashboard::getSettingsMenu();
			$item = new menuItem("apps");
			$item->setTitle(t("userpanel.apps"));
			$item->setURL(userpanel\url("settings/apps"));
			$item->setIcon("fa fa-cubes");
			$settings->addItem($item);
		}
	}
	protected $multiuser;
	public function __beforeLoad() {
		$this->multiuser = (bool) authorization::childrenTypes();
		$this->setTitle(t("userpanel.apps"));
		$this->addBodyClass("userpanel-apps");
		$this->setButtons();
		$this->setFormData();
		navigation::active("settings/apps");
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
		$this->setButton("apps_delete", $this->canDelete, array(
			"title" => t("userpanel.delete"),
			"icon" => "fa fa-times",
			"classes" => array("btn", "btn-xs", "btn-bricky"),
			"data" => array(
				"action" => "delete",
			),
		));
	}
	public function getComparisonsForSelect() {
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
