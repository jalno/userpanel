<?php
namespace packages\userpanel;

use packages\base\{date as baseDate, options, Translator};

class date extends baseDate {

	public static function format($format ,$timestamp = null){
		self::init();
		return parent::format($format, $timestamp);
	}

	public static function getTimeZone(): string {
		self::init();
		return parent::getTimeZone();
	}

	public static function strtotime($time,$now = null){
		self::init();
		return parent::strtotime($time, $now);
	}

	public static function mktime($hour = null, $minute = null, $second = null , $month = null, $day = null, $year = null){
		self::init();
		return parent::mktime($hour, $minute, $second, $month, $day, $year);
	}
	public static function relativeTime(int $time, string $format = "short"): string {
		self::init();
		return parent::relativeTime($time, $format);
	}

	public static function getFirstDayOfWeek(): int {
		self::init();
		return parent::getFirstDayOfWeek();
	}

	public static function setDefaultcalendar() {
		$calendar = "";
		$user = Authentication::getUser();
		if ($user) {
			$userOptions = $user->option('userpanel_date');
			if (isset($userOptions['calendar'])) {
				$calendar = $userOptions['calendar'];
			}
		}
		if (!$calendar) {
			$calendar = Translator::getLang()->getCalendar();
		}
		$option = Options::get('packages.userpanel.date');
		if (!$calendar and isset($option['calendar'])) {
			$calendar = $option['calendar'];
		}
		self::setCanlenderName($calendar);
		foreach (Translator::getLangs() as $lang) {
			if ($lang->getCalendar() == $calendar) {
				foreach ($lang->getDateFormats() as $key => $format) {
					self::setPresetsFormat($key, $format);
				}
				break;
			}
		}
	}

	public static function setDefaultTimeZone() {
		$user = Authentication::getUser();
		if ($user) {
			$userOptions = $user->option('userpanel_date');
			if (isset($userOptions['timezone'])) {
				parent::setTimeZone($userOptions['timezone']);
				return;
			}
		} 
		$option = Options::get('packages.userpanel.date');
		if ($option !== false and isset($option['timezone'])) {
			parent::setTimeZone($option['timezone']);
			return;
		}
		parent::setDefaultTimeZone();
	}

	public static function init() {
		if (self::$inited) {
			return;
		}
		self::setDefaultTimeZone();
		if(!self::$calendar){
			self::setDefaultcalendar();
		}
		self::$inited = true;
	}
}
