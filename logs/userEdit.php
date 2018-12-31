<?php
namespace packages\userpanel\logs;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{logs\panel, logs};
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
			$panel = new panel("userpanel.user.logs.userEdit.oldData");
			$panel->icon = "fa fa-trash";
			$panel->size = 6;
			$panel->title = translator::trans("userpanel.user.logs.userEdit.old.data");
			$html = "";
			if (isset($oldData["avatar"])) {
				$html .= '<div class="form-group">';
				$html .= '<label class="col-sm-6 col-xs-12 control-label">' . translator::trans("user.avatar") . ": </label>";
				$html .= '<div class="col-sm-6 col-xs-12">تغییر داده شد</div>';
				$html .= "</div>";
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
				} else {
					$title = translator::trans("log.user.{$field}");
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
					$html .= '<label class="col-sm-6 col-xs-12 control-label">'.translator::trans("userpnale.logs.userEdit.visibility_{$field}").': </label>';
					$html .= '<div class="col-sm-6 col-xs-12">عمومی</div>';
					$html .= "</div>";
				}
			}
			if (isset($newData["visibilities"])) {
				foreach ($newData["visibilities"] as $field) {
					$html .= '<div class="form-group">';
					$html .= '<label class="col-sm-6 col-xs-12 control-label">'.translator::trans("userpnale.logs.userEdit.visibility_{$field}").': </label>';
					$html .= '<div class="col-sm-6 col-xs-12">خصوصی</div>';
					$html .= "</div>";
				}
			}
			$panel->setHTML($html);
			$this->addPanel($panel);
		}
		if ($newData) {
			$panel = new panel("userpanel.user.logs.register");
			$panel->icon = "fa fa-plus";
			$panel->size = 6;
			$panel->title = translator::trans("userpanel.user.logs.userEdit.new.data");
			$html = "";
			if (isset($newData["avatar"])) {
				$html .= '<div class="form-group">';
				$html .= '<label class="col-sm-6 col-xs-12 control-label">' . translator::trans("user.avatar") . ": </label>";
				$html .= '<div class="col-sm-6 col-xs-12">تغییر داده شد</div>';
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
				} else {
					$title = translator::trans("log.user.{$field}");
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
					$html .= '<label class="col-sm-6 col-xs-12 control-label">'.translator::trans("userpnale.logs.userEdit.visibility_{$field}").': </label>';
					$html .= '<div class="col-sm-6 col-xs-12">عمومی</div>';
					$html .= "</div>";
				}
			}
			if (isset($oldData["visibilities"])) {
				foreach ($oldData["visibilities"] as $field) {
					$html .= '<div class="form-group">';
					$html .= '<label class="col-sm-6 col-xs-12 control-label">'.translator::trans("userpnale.logs.userEdit.visibility_{$field}").': </label>';
					$html .= '<div class="col-sm-6 col-xs-12">خصوصی</div>';
					$html .= "</div>";
				}
			}
			$panel->setHTML($html);
			$this->addPanel($panel);
		}
	}
}
