<?php
namespace themes\clipone\views\settings\usertypes;

use packages\base\Options;
use themes\clipone\{views\FormTrait, Navigation, ViewTrait};
use packages\userpanel\views\settings\usertypes\add as usertypeEdit;

class Add extends UsertypeEdit {
	use ViewTrait, FormTrait;

	function __beforeLoad() {
		$this->setTitle(array(
			t("settings"),
			t("usertype.add")
		));
		Navigation::active("settings/usertypes");
		$this->addBodyClass("usertypes");
		$this->addBodyClass("add-usertype");
		$this->dynamicData()->setData("permissions", $this->buildPermissionsArray());
	}
	public function export(): array {
		$export = array(
			"data" => array(
				"permissions" => $this->buildPermissionsArray(true),
			)
		);
		return $export;
	}
	protected function buildPermissionsArray(bool $withTranslate = false): array {
		$disabledPermissions = Options::get("packages.userpanel.disabledpermisions");
		$disabledPermissions = ($disabledPermissions and is_array($disabledPermissions)) ? $disabledPermissions : [];
		$permissions = array();
		foreach ($this->getPermissions() as $permission) {
			if (in_array($permission, $disabledPermissions)) continue;
			$item = array(
				"key" => $permission,
			);
			if ($withTranslate) {
				$item["title"] = $this->translatePermission($permission);
			}
			$permissions[] = $item;
		}
		return $permissions;
	}
	protected function translatePermission($permission) {
		$trans = t("usertype.permissions." . $permission);
		return ($trans ? $trans : $permission);
	}
}
