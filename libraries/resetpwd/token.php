<?php
namespace packages\userpanel\resetpwd;
use packages\base\db\dbObject;
class token extends dbObject{
	protected $dbTable = "userpanel_resetpwd_token";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'sent_at' => array('type' => 'int', 'required' => true),
        'user' => array('type' => 'int', 'required' => true),
		'token' => array('type' => 'text', 'required' => true),
        'ip' => array('type' => 'text', 'required' => true)
    );
	protected $relations = array(
        'user' => array("hasOne", "packages\\userpanel\\user", "user"),
	);
}
