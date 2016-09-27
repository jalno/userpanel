<?php
namespace themes\clipone;
use \themes\clipone\navigation\menuItem;
class navigation{
	static $menu = array();
	static $active = array();
	static function addItem(menuItem $item){
		$reversed = array_reverse(self::$menu);
		$reversed[$item->getName()] = $item;
		self::$menu = array_reverse($reversed);
		if($item->getName() == 'dashboard'){
			breadcrumb::prependItem($item);
		}
	}
	static function active($active){
		self::$active = explode("/", $active, 2);

	}
	static function build(){
		$html = "";
		uasort(self::$menu, array(__CLASS__, 'sort'));

		foreach(self::$menu as $name => $item){
			if(self::$active and $name == self::$active[0]){
				breadcrumb::addItem($item);
				$item->active(isset(self::$active[1]) ? self::$active[1] : true);
			}
			$html .= $item->build();
		}
		return $html;
	}
	static function sort($a, $b){
		if ($a->getPriority() == $b->getPriority()) {
			return 0;
		}
		return ($a->getPriority() < $b->getPriority()) ? -1 : 1;
	}
}
