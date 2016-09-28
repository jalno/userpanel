<?php
namespace packages\userpanel;
use packages\base\db\dbObject;
class usertype extends dbObject{
	protected $dbTable = "userpanel_usertypes";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'title' => array('type' => 'text', 'required' => true),
    );
    protected $relations = array(
        'permissions' => array("hasMany", "packages\\userpanel\\usertype_permission", "type"),
		'children' => array("hasMany", "packages\\userpanel\\usertype_priority", "parent"),
		'options' => array('hasMany', 'packages\\userpanel\\usertype_option', 'usertype')
    );
    public function hasPermission($permission){
    	foreach($this->permissions as $p){
    		if($p->name == $permission){
    			return true;
    		}
    	}
    	return false;
    }
	public function option($name){
		foreach($this->options as $option){
			if($option->name == $name){
				return $option->value;
			}
		}
		return null;
	}
}
