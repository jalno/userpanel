<?php
namespace packages\userpanel\controllers;

use packages\base\Date;
use packages\userpanel\{events\settings\Controller, events\settings\Log, User};

class Settings implements Controller {

	public function store(array $inputs, User $user): array {
		$logs = array();
		$oldValue = $user->option("userpanel_timezone");
		if (!$oldValue) {
			$oldValue = Date::getTimeZone();
		}
		if (isset($inputs["userpanel_timezone"]) and $oldValue != $inputs["userpanel_timezone"]) {
			$logs[] = new Log("userpanel_timezone", $oldValue, $inputs["userpanel_timezone"], t("userpanel.usersettings.message.timezone"));
			$user->setOption("userpanel_timezone", $inputs["userpanel_timezone"]);
		}
		return $logs;
	}

}
