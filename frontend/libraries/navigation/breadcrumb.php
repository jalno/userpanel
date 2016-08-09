<?php
namespace themes\clipone;
use \themes\clipone\navigation\menuItem;
class breadcrumb{
	static $menu = array();
	static function addItem(menuItem $item){
		self::$menu[$item->getName()] = $item;
	}
	static function prependItem(menuItem $item){
		$menu = array_reverse(self::$menu, true);
		$menu[$item->getName()] = $item;
		self::$menu = array_reverse($menu, true);
	}
	static function build(){
		$html = "";
		$len = count(self::$menu);
		$x = 0;
		foreach(self::$menu as $item){
			$icon = $item->getIcon();
			if($x != $len -1){
				$link = $item->getURL();
			}else{
				$link = false;
			}
			$html .= "<li".($x == $len -1 ? " class=\"active\"" : "")."> ";
			if($icon){
				$html .= "<i class=\"{$icon}\"></i> ";
			}
			if($link){
				$html .= "<a href=\"{$link}\">";
			}
			$html .= $item->getTitle();
			if($link){
				$html .= "</a>";
			}
			$html .= "</li>";
			$x++;
		}
		return $html;
	}
}
