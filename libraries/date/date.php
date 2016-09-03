<?php
namespace packages\userpanel;
use \packages\base\date as baseDate;
use \packages\base\options;
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
		if(($option = options::load('packages.userpanel.date')) !== false){
			parent::setCanlenderName($option['calendar']);
			self::$calendar = $option['calendar'];
		}
	}
}
