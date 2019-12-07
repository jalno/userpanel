<?php
namespace packages\userpanel\listeners;

use packages\base\{Date, Options, Translator};
use packages\userpanel\events\settings as SettingsEvent;
use packages\userpanel\controllers\Settings as Controller;
use \DateTimeZone;
use \DateTime;

class Settings {
	
	public function settingsList(SettingsEvent $settings){
		$tuning = new settingsEvent\Tuning('userpanel');
		$tuning->setController(Controller::class);
		$tuning->addInput(array(
			'name' => 'userpanel_timezone',
			'type' => 'string',
			'values' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
		));
		$tuning->addInput(array(
			'name' => 'userpanel_calendar',
			'type' => 'string',
			'values' => array(
				"jdate",
				"gregorian",
			),
		));
		$tuning->addField(array(
			'name' => 'userpanel_timezone',
			'type' => 'select',
			'label' => t('userpanel.usersettings.message.timezone'),
			'options' => $this->getTimeZonesForSelect(),
		));
		$tuning->addField(array(
			'name' => 'userpanel_calendar',
			'type' => 'select',
			'label' => t('userpanel.usersettings.message.calendar'),
			'options' => array(
				array(
					'title' => t('userpanel.usersettings.message.calendar.jdate'),
					'value' => 'jdate',
				),
				array(
					'title' => t('userpanel.usersettings.message.calendar.gregorian'),
					'value' => 'gregorian',
				),
			),
		));
		$userCustoms = $settings->getUser()->option('userpanel_date');
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
		$tuning->setDataForm('userpanel_timezone', $timeZone);
		$tuning->setDataForm('userpanel_calendar', $calendar);
		$settings->addTuning($tuning);
	}

	private function getTimeZonesForSelect() {
		$timezones = array();
		static $regions = array(
			'Asia' => DateTimeZone::ASIA,
			'Europe' => DateTimeZone::EUROPE,
			'America' => DateTimeZone::AMERICA,
			'Africa' => DateTimeZone::AFRICA,
			'Australia' => DateTimeZone::AUSTRALIA,
			'Antarctica' => DateTimeZone::ANTARCTICA,
			'Atlantic' => DateTimeZone::ATLANTIC,
			'Indian' => DateTimeZone::INDIAN,
			'Pacific' => DateTimeZone::PACIFIC,
		);
		foreach ($regions as $key => $region) {
			foreach (DateTimeZone::listIdentifiers($region) as $tz) {
				$tzOffset = (new DateTimeZone($tz))->getOffset(new DateTime);
				$timezones[t("date.timezone." . $key)][] = array(
					'title' => t('date.timezone.'.$tz, array(
						'timezone' => ($tzOffset < 0 ? '-' : '+') . gmdate('H:i', abs($tzOffset)),
					)),
					'value' => $tz,
				);
			}
			asort($timezones[t("date.timezone." . $key)]);
		}
		return $timezones;
	}

}
