<?php
namespace themes\clipone;
use \themes\clipone\navigation\menuItem;
class navigation{
	static $menu = array();
	static $active = array();
	static function addItem(menuItem $item){
		$found = false;
		foreach(self::$menu as $x => $menuItem){
			if($menuItem->getName() == $item->getName()){
				$found = $x;
				break;
			}
		}
		if($found === false){
			if(!$item->getPriority()){
				$item->setPriority(count(self::$menu)*100);
			}
			self::$menu[] = $item;
		}else{
			if(!$item->getPriority()){
				$item->setPriority(self::$menu[$found]->getPriority());
			}
			self::$menu[$found] = $item;
		}
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

		foreach(self::$menu as $item){
			$name = $item->getName();
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
	static function getByName($name){
		if(substr($name, -1) == '/'){
			$name = substr($name, 0, strlen($name)-1);
		}
		$names = explode("/", $name, 2);
		$name = $names[0];
		foreach(self::$menu as $item){
			if($item->getName() == $name){
				if(isset($names[1]) and $names[1]){
					return $item->getByName($names[1]);
				}else{
					return $item;
				}
			}
		}
		return null;
	}
}
