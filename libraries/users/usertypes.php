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
        'permissions' => array("hasMany", "packages\\userpanel\\usertype_permission", "type")
    );
    public function hasPermission($permission){
    	foreach($this->permissions as $p){
    		if($p->name == $permission){
    			return true;
    		}
    	}
    	return false;
    }
}
