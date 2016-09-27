<?php
namespace packages\userpanel;
use \packages\base\options;
class frontend{
	static private $logo;
	static private $copyright;
	static function setLogoHTML($logo){
		self::$logo = $logo;
	}
	static function getLogoHTML(){
		if(!self::$logo){
			self::$logo = options::get('packages.userpanel.frontend.logo');
		}
		return self::$logo;
	}
	static function setCopyRightHTML($copyright){
		self::$copyright = $copyright;
	}
	static function getCopyRightHTML(){
		if(!self::$copyright){
			self::$copyright = options::get('packages.userpanel.frontend.copyright');
		}
		return self::$copyright;
	}
}
