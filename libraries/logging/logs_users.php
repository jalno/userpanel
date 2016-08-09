<?php
namespace packages\userpanel;
use packages\base\db\dbObject;
use packages\base\http;
class log_user extends dbObject{
	protected $dbTable = "userpanel_logs_users";
	protected $primaryKey = "";
	protected $dbFields = array(
        'log' => array('type' => 'int', 'required' => true),
        'user' => array('type' => 'int', 'required' => true)
    );
	protected $relations = array(
		'user' => array("hasOne", "packages\\userpanel\\user", "id")
    );
}
