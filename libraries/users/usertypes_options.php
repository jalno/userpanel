<?php
namespace packages\userpanel;
use packages\base\db\dbObject;
class usertype_option extends dbObject{
	protected $dbTable = "userpanel_usertypes_options";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'usertype' => array('type' => 'int', 'required' => true),
		'name' => array('type' => 'text', 'required' => true),
        'value' => array('type' => 'text', 'required' => true)
    );
	protected $relations = array(
        'usertype' => array("hasOne", "packages\\userpanel\\usertype", "usertype"),
	);
}
