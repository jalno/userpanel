<?php
namespace packages\userpanel\controllers;

use packages\base\{Date, Options, Translator};
use packages\userpanel\{events\settings\Controller, events\settings\Log, User};

class Settings implements Controller {

	public function store(array $inputs, User $user): array {
		$logs = array();
		$changed = false;
		$userCustoms = $user->option('userpanel_date');
		if (isset($inputs['userpanel_calendar'])) {
			$calendar = '';
			if (isset($userCustoms['calendar'])) {
				$calendar = $userCustoms['calendar'];
			}
			if (!$calendar) {
				$calendar = Translator::getLang()->getCalendar();
			}
			$option = Options::get('packages.userpanel.date');
			if (!$calendar and isset($option['calendar'])) {
				$calendar = $option['calendar'];
			}
			if ($inputs['userpanel_calendar'] != $calendar) {
				$userCustoms['calendar'] = $inputs['userpanel_calendar'];
				$logs[] = new Log('userpanel_calendar', $calendar, $inputs['userpanel_calendar'], t('userpanel.usersettings.message.calendar'));
				$changed = true;
			}
		}
		if (isset($inputs['userpanel_timezone'])) {
			$timeZone = '';
			if (isset($userCustoms['timezone'])) {
				$timeZone = $userCustoms['timezone'];
			}
			$option = Options::get('packages.userpanel.date');
			if (!$timeZone and $option !== false and isset($option['timezone'])) {
				$timeZone = $option['timezone'];
			}
			if (!$timeZone) {
				$timeZone = Date::getTimeZone();
			}
			if ($timeZone != $inputs['userpanel_timezone']) {
				$userCustoms['timezone'] = $inputs['userpanel_timezone'];
				$logs[] = new Log('userpanel_timezone', $timeZone, $inputs['userpanel_timezone'], t('userpanel.usersettings.message.timezone'));
				$changed = true;
			}
		}
		if ($changed) {
			$user->setOption('userpanel_date', $userCustoms);
		}
		return $logs;
	}

}
