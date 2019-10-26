<?php
namespace packages\userpanel;
use packages\base\{date as baseDate, options, Translator};

class date extends baseDate{
	protected static $calendar;
	public static function format($format ,$timestamp = null){
		if(!self::$calendar){
			self::setDefaultcalendar();
		}
		return parent::format($format ,$timestamp);
	}
	public static function strtotime($time,$now = null){
		if(!self::$calendar){
			self::setDefaultcalendar();
		}
		return parent::strtotime($time ,$now);
	}
	public static function mktime($hour = null, $minute = null, $second = null , $month = null, $day = null, $year = null, $is_dst = -1){
		if(!self::$calendar){
			self::setDefaultcalendar();
		}
		return parent::mktime($hour, $minute, $second, $month, $day, $year, $is_dst);
	}
	public static function setDefaultcalendar(){
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
	public static function getCanlenderName(){
		if(!self::$calendar){
			self::setDefaultcalendar();
		}
		return self::$calendar;
	}
}
