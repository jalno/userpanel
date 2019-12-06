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

	public static function setDefaultcalendar() {
		$calendar = "";
		if ($user = Authentication::getUser()) {
			if ($lang = Translator::getCodeLang() and $option = $user->option("packages.base.date")) {
				if (isset($option[$lang])) {
					$calendar = $option[$lang]["calendar"];
				}
			}
		}
		if (!$calendar) {
			if ($langCalendar = Translator::getLang()->getCalendar()) {
				$calendar = $langCalendar;
			} elseif (($option = options::load('packages.userpanel.date')) !== false) {
				$calendar = $option['calendar'];
			}
		}
		parent::setCanlenderName($calendar);
		self::$calendar = $calendar;
		foreach (Translator::getLangs() as $lang) {
			if ($lang->getCalendar() == $calendar) {
				foreach ($lang->getDateFormats() as $key => $format) {
					parent::setPresetsFormat($key, $format);
				}
				break;
			}
		}
	}

	public static function setDefaultTimeZone() {
		$user = Authentication::getUser();
		if ($user) {
			$userOption = $user->option('userpanel_timezone');
			if ($userOption) {
				parent::setTimeZone($userOption);
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
