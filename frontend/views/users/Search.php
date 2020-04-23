<?php
namespace themes\clipone\views\users;

use packages\userpanel;
use packages\base\Http;
use packages\userpanel\{User, Authorization, views\users\Search as ParentView};
use themes\clipone\{viewTrait, views\ListTrait, views\FormTrait, Navigation};

class Search extends ParentView {

	public static function onSourceLoad() {
		parent::onSourceLoad();
		if (!parent::$navigation) {
			return;
		}
		$item = new Navigation\MenuItem("users");
		$item->setTitle(t("users"));
		$item->setURL(userpanel\url("users"));
		$item->setIcon("clip-users");
		Navigation::addItem($item);
	}

	use viewTrait, ListTrait, FormTrait;

	protected $types = array();

	public function __beforeLoad() {
		$this->setTitle(t("users"));
		$this->setButtons();
		Navigation::active("users");
	}

	protected function setButtons(): void {
		$this->setButton("view", $this->canView, array(
			"title" => t("user.profile"),
			"icon" => "fa fa-credit-card",
			"classes" => array("btn", "btn-xs", "btn-green")
		));
		$this->setButton("edit", $this->canEdit, array(
			"title" => t("user.edit"),
			"icon" => "fa fa-edit",
			"classes" => array("btn", "btn-xs", "btn-teal")
		));
		$this->setButton("delete", $this->canDelete, array(
			"title" => t("user.delete"),
			"icon" => "fa fa-times",
			"classes" => array("btn", "btn-xs", "btn-bricky")
		));
	}

	protected function getSelectedTypes(): array {
		$selected = $this->getDataForm("type-select");
		return ($selected and is_array($selected)) ? $selected : [];
	}

	protected function getTypesForSelect(): array {
		$options = [];
		foreach($this->getUserTypes() as $type){
			$options[] = array(
				"title" => $type->title,
				"value" => $type->id
			);
		}
		return $options;
	}

	protected function getStatusForSelect(): array {
		return array(
			array(
				"title" => "",
				"value" => ""
			),
			array(
				"title" => t("user.status.active"),
				"value" => user::active
			),
			array(
				"title" => t("user.status.suspend"),
				"value" => user::suspend
			),
			array(
				"title" => t("user.status.deactive"),
				"value" => user::deactive
			)
		);
	}

	protected function getComparisonsForSelect(): array {
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

	protected function getCountriesForSelect(): array {
		$options = [array("title" => "", "value" => "")];
		foreach ($this->getCountries() as $country) {
			$options[] = array(
				"title" => $country->name,
				"value" => $country->id
			);
		}
		return $options;
	}

	protected function getFormData(): array {
		return Http::$request["get"] ?? array();
	}
}
