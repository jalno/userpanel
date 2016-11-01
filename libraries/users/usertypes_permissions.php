<?php
namespace packages\userpanel;
use packages\base\db\dbObject;
class usertype_permission extends dbObject{
	protected $dbTable = "userpanel_usertypes_permissions";
	protected $primaryKey = "";
	protected $dbFields = array(
        'type' => array('type' => 'int', 'required' => true),
        'name' => array('type' => 'text', 'required' => true)
    );
	protected $relations = array(
        'type' => array("hasOne", "packages\\userpanel\\usertype", "type")
    );
}
