<?php
namespace packages\userpanel;
use packages\base\db\dbObject;
class usertype extends dbObject{
	const admin = 1;
	const support = 2;
	const guest = 3;
	protected $dbTable = "userpanel_usertypes";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'title' => array('type' => 'text', 'required' => true),
    );
    protected $relations = array(
        'permissions' => array("hasMany", "packages\\userpanel\\usertype\permission", "type"),
		'children' => array("hasMany", "packages\\userpanel\\usertype\priority", "parent"),
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
