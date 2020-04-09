<?php
namespace themes\clipone\views\settings\usertypes;

use packages\base\Options;
use packages\userpanel\Usertype;
use themes\clipone\{views\FormTrait, Navigation, ViewTrait};
use packages\userpanel\views\settings\usertypes\Edit as UsertypeEdit;

class Edit extends UsertypeEdit {
	use ViewTrait, FormTrait;

	function __beforeLoad() {
		$this->setTitle(array(
			t("settings"),
			t("usertype.edit")
		));
		Navigation::active("settings/usertypes");
		$this->addBodyClass("usertypes");
		$this->addBodyClass("edit-usertype");
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
		$usertype = $this->getUserType();
		$permissions = array();
		foreach ($this->getPermissions() as $permission) {
			if (in_array($permission, $disabledPermissions)) continue;
			$item = array(
				"key" => $permission,
				"value" => $usertype->hasPermission($permission),
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
	protected function hasPriority(Usertype $priority) {
		foreach ($this->getUserType()->children as $child) {
			if ($priority->id == $child->data["child"]) {
				return true;
			}
		}
		return false;
	}
}
