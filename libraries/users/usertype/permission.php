<?php
namespace packages\userpanel\usertype;
use packages\base\db;
use packages\base\db\dbObject;
class permission extends dbObject{
	protected $dbTable = "userpanel_usertypes_permissions";
	protected $primaryKey = "";
	protected $dbFields = array(
        'type' => array('type' => 'int', 'required' => true),
        'name' => array('type' => 'text', 'required' => true)
    );
	protected $relations = array(
        'type' => array("hasOne", "packages\\userpanel\\usertype", "type")
    );
	public function delete(){
		db::where("type", $this->data['type']);
		db::where("name", $this->name);
		return db::delete($this->dbTable);
	}
}
