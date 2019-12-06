<?php
namespace packages\userpanel\listeners;

use packages\base\Date;
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
			'values' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
		));
		$field = array(
			'name' => 'userpanel_timezone',
			'type' => 'select',
			'label' => t("userpanel.usersettings.message.timezone"),
			'options' => $this->getTimeZonesForSelect(),
		);
		$tuning->addField($field);
		$dataform = Date::getTimeZone();
		$userTimeZone = $settings->getUser()->option('userpanel_timezone');
		if ($userTimeZone) {
			$dataform = $userTimeZone;
		}
		$tuning->setDataForm('userpanel_timezone', $dataform);
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
