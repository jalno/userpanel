<?php
namespace themes\clipone\views\settings\usertypes;

use function packages\userpanel\url;

use packages\base\{view\Error, Options};
use packages\userpanel\{Usertype, views\settings\usertypes\Edit as UsertypeEdit};
use themes\clipone\{views\FormTrait, Navigation, ViewTrait, views\UsertypesTrait};

class Edit extends UsertypeEdit {

	use ViewTrait, FormTrait, UsertypesTrait;

	public function __beforeLoad() {
		$this->setTitle(array(
			t("settings"),
			t("usertype.edit")
		));
		Navigation::active("settings/usertypes");
		$this->addBodyClass("usertypes");
		$this->addBodyClass("edit-usertype");
		$this->dynamicData()->setData("usertypePermissions", $this->buildPermissionsArray());
		$this->addWarnings();
	}
	public function export(): array {
		$permissions = $this->buildPermissionsArray(true);
		return array(
			"data" => array(
				"count" => count($permissions),
				"permissions" => $permissions,
			)
		);
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
	protected function addWarnings(): void {
		$hasCustomizedPermissionsUser = $this->hasCustomizedPermissionsUser();
		if ($hasCustomizedPermissionsUser) {
			$type = $this->getUserType();
			$error = new Error("packages.userpanel.usertypes.edit.has_custom_permissions_users");
			$error->setType(Error::WARNING);
			$error->setData(array(
				array(
					"txt" => '<i class="fa fa-search"></i> ' . t("error.packages.userpanel.usertypes.edit.has_custom_permissions_users.view_users"),
					"type" => "btn-warning",
					"link" => url("users", array(
						"type" => $type->id,
						"has_custom_permissions" => true,
					)),
				),
			), "btns");
			$this->addError($error);
		}
	}
}
