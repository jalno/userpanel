<?php
namespace packages\userpanel\user;
use packages\base\db\dbObject;
class option extends dbObject{
	protected $dbTable = "userpanel_users_options";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'user' => array('type' => 'int', 'required' => true),
		'name' => array('type' => 'text', 'required' => true),
        'value' => array('type' => 'text', 'required' => true)
    );
	protected $relations = array(
        'user' => array("hasOne", "packages\\userpanel\\user", "user"),
	);
	protected $jsonFields = array('value');
}
