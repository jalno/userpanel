<?php
namespace packages\userpanel\logs\usertypes;

use packages\userpanel\{Usertype};

trait UsertypeTrait {
	public function getHTML(array $data): string {
		$html = "";

		if (isset($data["usertype"]) and $data["usertype"]) {
			if (isset($data["usertype"]["title"])) {
				$html .= '<div class="form-group">';
					$html .= '<label class="col-sm-6 col-xs-12 control-label">' . t("usertype.title") . ": </label>";
					$html .= '<div class="col-sm-6 col-xs-12">' . $data["usertype"]["title"] . "</div>";
				$html .= "</div>";
			}
		}

		if (isset($data["permissions"]) and $data["permissions"]) {
			$html .= '<div class="form-group">';
				$html .= '<label class="col-sm-6 col-xs-12 control-label">' . t("usertype.permissions") . ": </label>";
				$html .= '<div class="col-sm-6 col-xs-12">';
					$html .= '<ul class="list-group">';
					foreach ($data["permissions"] as $permission) {
						$html .= '<li class="list-group-item">' . t("usertype.permissions.{$permission}") . '</li>';
					}
					$html .= "</ul>";
				$html .= "</div>";
			$html .= "</div>";
		}

		if (isset($data["priorities"]) and $data["priorities"]) {
			$html .= '<div class="form-group">';
				$html .= '<label class="col-sm-6 col-xs-12 control-label">' . t("usertype.periorities") . ": </label>";
				$html .= '<div class="col-sm-6 col-xs-12">';
					$html .= '<ul class="list-group">';
					foreach ($data["priorities"] as $periority) {
						$usertype = (new Usertype)->byId($periority);
						$html .= '<li class="list-group-item' . (!$usertype ? ' ltr' : '') . '">' . ($usertype ? $usertype->title : "#{$periority}") . '</li>';
					}
					$html .= "</ul>";
				$html .= "</div>";
			$html .= "</div>";
		}
		
		return $html;
	}
}
