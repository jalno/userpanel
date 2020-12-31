<?php
namespace packages\userpanel\logs;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{logs\panel, logs,usertype};
class userEdit extends logs{
	public function getColor():string{
		return "circle-teal";
	}
	public function getIcon():string{
		return "fa fa-edit";
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;
		$oldData = $parameters["oldData"];
		$newData = $parameters["newData"];
		if ($oldData) {
			$panel = new Panel("userpanel.user.logs.userEdit.oldData");
			$panel->icon = "fa fa-trash";
			$panel->size = 6;
			$panel->title = t("userpanel.user.logs.userEdit.old.data");
			$html = "";
			if (isset($oldData["avatar"])) {
				unset($oldData["avatar"]);
			}
			foreach ($oldData as $field => $val) {
				if ($field == "visibilities") {
					continue;
				}
				if (is_array($val) and isset($val["title"], $val["value"])) {
					$title = $val["title"];
					$value = $val["value"];
					$isLtr = !is_string($value);
				} else if ($field == "type") {
						$title = t("log.user.{$field}");
						$types = new usertype();
						$types->where("id",$val);
						$types = $types->getOne();
						if ($types != null) {
							$value = $types->title;
						} else {
							$value = $val;	
						}
						$isLtr = "";
				} else if ($field == "has_custom_permissions") {
					$title = t("packages.userpanel.logs.userEdit.has_custom_permissions");
					$value = $val === true ? t("packages.userpanel.logs.userEdit.yes") : t("packages.userpanel.logs.userEdit.no");
				} else {
					$title = t("log.user.{$field}");
					$value = $val;
					$isLtr = !in_array($field, ["name", "lastname"]);
				}
				$html .= '<div class="form-group">';
				$html .= '<label class="col-sm-6 col-xs-12 control-label">' . $title . ": </label>";
				$html .= '<div class="col-sm-6 col-xs-12' . ($isLtr ? " ltr" : "") . '">' . $value . "</div>";
				$html .= "</div>";
			}
			if (isset($oldData["visibilities"])) {
				foreach ($oldData["visibilities"] as $field) {
					$html .= '<div class="form-group">';
					$html .= '<label class="col-sm-6 col-xs-12 control-label">'.t("userpnale.logs.userEdit.visibility_{$field}").': </label>';
					$html .= '<div class="col-sm-6 col-xs-12">عمومی</div>';
					$html .= "</div>";
				}
			}
			if (isset($newData["visibilities"])) {
				foreach ($newData["visibilities"] as $field) {
					$html .= '<div class="form-group">';
					$html .= '<label class="col-sm-6 col-xs-12 control-label">'.t("userpnale.logs.userEdit.visibility_{$field}").': </label>';
					$html .= '<div class="col-sm-6 col-xs-12">خصوصی</div>';
					$html .= "</div>";
				}
			}
			if ($html) {
				$panel->setHTML($html);
				$this->addPanel($panel);
			}
		}
		if ($newData) {
			$panel = new Panel("userpanel.user.logs.register");
			$panel->icon = "fa fa-plus";
			$panel->size = 6;
			$panel->title = t("userpanel.user.logs.userEdit.new.data");
			$html = "";
			if (isset($newData["avatar"])) {
				$html .= '<div class="form-group">';
				$html .= '<label class="col-sm-6 col-xs-12 control-label">' . t("user.avatar") . ": </label>";
				$html .= '<div class="col-sm-6 col-xs-12">' . t("packages.userpanel.logs.userEdit.changed") . '</div>';
				$html .= "</div>";
				unset($newData['avatar']);
			}
			foreach ($newData as $field => $val) {
				if ($field == "visibilities") {
					continue;
				}
				if (is_array($val) and isset($val["title"], $val["value"])) {
					$title = $val["title"];
					$value = $val["value"];
					$isLtr = !is_string($value);
				} else if ($field == "type") {
					$title = t("log.user.{$field}");
					$types = new usertype();
					$types->where("id",$val);
					$types = $types->getOne();
					if ($types != null) {
						$value = $types->title;
					} else {
						$value = $val;	
					}
					$isLtr = "";
				} else if ($field == "has_custom_permissions") {
					$title = t("packages.userpanel.logs.userEdit.has_custom_permissions");
					$value = $val === true ? t("packages.userpanel.logs.userEdit.yes") : t("packages.userpanel.logs.userEdit.no");
				} else {
					$title = t("log.user.{$field}");
					$value = $val;
					$isLtr = !in_array($field, ["name", "lastname"]);
				}
				$html .= '<div class="form-group">';
				$html .= '<label class="col-sm-6 col-xs-12 control-label">' . $title . ": </label>";
				$html .= '<div class="col-sm-6 col-xs-12' . ($isLtr ? " ltr" : "") . '">' . $value . "</div>";
				$html .= "</div>";
			}
			if (isset($newData["visibilities"])) {
				foreach ($newData["visibilities"] as $field) {
					$html .= '<div class="form-group">';
					$html .= '<label class="col-sm-6 col-xs-12 control-label">'.t("userpnale.logs.userEdit.visibility_{$field}").': </label>';
					$html .= '<div class="col-sm-6 col-xs-12">عمومی</div>';
					$html .= "</div>";
				}
			}
			if (isset($oldData["visibilities"])) {
				foreach ($oldData["visibilities"] as $field) {
					$html .= '<div class="form-group">';
					$html .= '<label class="col-sm-6 col-xs-12 control-label">'.t("userpnale.logs.userEdit.visibility_{$field}").': </label>';
					$html .= '<div class="col-sm-6 col-xs-12">خصوصی</div>';
					$html .= "</div>";
				}
			}
			if ($html) {
				$panel->setHTML($html);
				$this->addPanel($panel);
			}
		}
		if (isset($parameters["permissions"])) {
			if (isset($parameters["permissions"]['addedPermissions']) and $parameters["permissions"]['addedPermissions']) {
				$panel = new Panel("userpanel.user.logs.permission.added_permissions");
				$panel->icon = "fa fa-plus-circle";
				$panel->size = 6;
				$panel->title = t("packages.userpanel.logs.userEdit.permissions.added_permissions");
				$html = "";
				foreach ($parameters["permissions"]['addedPermissions'] as $permission) {
					$html .= '<div class="form-group">';
						$html .= '<label class="col-sm-4 col-xs-12 control-label">' . t("packages.userpanel.logs.userEdit.permissions.permission") . ': </label>';
						$html .= '<div class="col-sm-8 col-xs-12"><i class="fa fa-check" style="color: green;"></i> ' . t("usertype.permissions.{$permission}") . '</div>';
					$html .= "</div>";
				}
				$panel->setHTML($html);
				$this->addPanel($panel);
			}
			if (isset($parameters["permissions"]['removedPermissions']) and $parameters["permissions"]['removedPermissions']) {
				$panel = new Panel("userpanel.user.logs.permission.added_permissions");
				$panel->icon = "fa fa-ban";
				$panel->size = 6;
				$panel->title = t("packages.userpanel.logs.userEdit.permissions.removed_permissions");
				$html = "";
				foreach ($parameters["permissions"]['removedPermissions'] as $permission) {
					$html .= '<div class="form-group">';
						$html .= '<label class="col-sm-4 col-xs-12 control-label">' . t("packages.userpanel.logs.userEdit.permissions.permission") . ': </label>';
						$html .= '<div class="col-sm-8 col-xs-12"> <i class="fa fa-times" style="color: red;"></i> ' . t("usertype.permissions.{$permission}") . '</div>';
					$html .= "</div>";
				}
				$panel->setHTML($html);
				$this->addPanel($panel);
			}
		}
	}
}
