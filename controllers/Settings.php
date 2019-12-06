<?php
namespace packages\userpanel\controllers;

use packages\base\Date;
use packages\userpanel\{events\settings\Controller, events\settings\Log, User};

class Settings implements Controller {

	public function store(array $inputs, User $user): array {
		$logs = array();
		$oldTimeZone = $user->option("userpanel_timezone");
		if (!$oldTimeZone) {
			$oldTimeZone = Date::getTimeZone();
		}
		if (isset($inputs["userpanel_timezone"]) and $oldTimeZone != $inputs["userpanel_timezone"]) {
			$logs[] = new Log("userpanel_timezone", $oldTimeZone, $inputs["userpanel_timezone"], t("userpanel.usersettings.message.timezone"));
			$user->setOption("userpanel_timezone", $inputs["userpanel_timezone"]);
		}
		$oldCalendar = $user->option("userpanel_calendar");
		if (!$oldCalendar) {
			$oldCalendar = Date::getCanlenderName();
		}
		if (isset($inputs["userpanel_calendar"]) and $oldCalendar != $inputs["userpanel_calendar"]) {
			$logs[] = new Log("userpanel_calendar", $oldCalendar, $inputs["userpanel_calendar"], t("userpanel.usersettings.message.calendar"));
			$user->setOption("userpanel_calendar", $inputs["userpanel_calendar"]);
		}
		return $logs;
	}

}
