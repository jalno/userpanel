<?php
namespace themes\clipone\views\users;

use packages\userpanel;
use packages\base\Http;
use packages\userpanel\{User, Authorization, views\users\Search as ParentView};
use themes\clipone\{viewTrait, views\ListTrait, views\FormTrait, Navigation, views\CountryCodeToReigonCodeTrait};

class Search extends ParentView {
	use CountryCodeToReigonCodeTrait;

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

	public bool $canAdd;
	protected $types = array();

	public function __construct() {
		parent::__construct();
		$this->canAdd = Authorization::is_accessed("users_add", "userpanel");
	}

	public function __beforeLoad() {
		$this->setTitle(t("users"));
		$this->setButtons();
		$this->addBodyClass("userpanel");
		$this->addBodyClass("users-search");
		Navigation::active("users");
		$this->prepareDynamicData();
	}

	public function export() {
		return array(
			'data' => array(
				'items' => array_map(function (User $user) {
					$array = $user->toArray(false);
					$array["cellphone"] = $user->getCellphoneWithDialingCode();
					$array["phone"] = $user->getPhoneWithDialingCode();
					return $array;
				}, $this->dataList),
				'items_per_page' => (int)$this->itemsPage,
				'current_page' => (int)$this->currentPage,
				'total_items' => (int)$this->totalItems
			)
		);
	}

	/**
	 * @param string[]|string $statuses
	 * @param array<string|int>|string|int $values
	 */
	public function isActiveStatusTab($statuses, $values): bool {
		static $otherTabsIsActive = false;

		if ($otherTabsIsActive) {
			return false;
		}

		$urlParameters = $this->getFormData();

		$statuses = (array) $statuses;
		$values = (array) $values;

		foreach ($statuses as $key => $status) {
			$value = ($values[$key] ?? $values[0]);
			if ((!$value and !isset($urlParameters[$status])) or (isset($urlParameters[$status]) and $urlParameters[$status] == $value)) {
				$otherTabsIsActive = true;
				return true;
			}
		}

		return false;
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

	private function prepareDynamicData(): void {
		$dd = $this->dynamicData();
		$dd->setData("countriesCode", $this->generateCountiesArray());
		$dd->setData("defaultCountryCode", $this->getDefaultCountryCode());
	}
}
