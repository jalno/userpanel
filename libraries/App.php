<?php
namespace packages\userpanel;
use packages\base;
use packages\base\db\dbObject;

class App extends dbObject {
	protected $dbTable = "userpanel_apps";
	protected $primaryKey = "id";
	protected $dbFields = array(
		"name" => array("type" => "text", "required" => true),
		"user" => array("type" => "int", "required" => true),
		"token" => array("type" => "text", "required" => true, "unique" => true),
	);
	protected $relations = array(
		"user" => array("hasOne", user::class, "user")
	);
}
