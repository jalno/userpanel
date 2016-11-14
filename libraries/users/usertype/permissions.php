<?php
namespace packages\userpanel\usertype;
use \packages\base\events;
use \packages\userpanel\events\usertype_permissions_list;
class permissions{
	static protected $permissions = array();
	static public function add($permission){
		if(!in_array($permission, self::$permissions)){
			self::$permissions[] = $permission;
		}
	}
	static public function get(){
		events::trigger(new usertype_permissions_list());
		return self::$permissions;
	}
	static public function has($permission){
		return in_array($permission, self::$permissions);
	}
}
