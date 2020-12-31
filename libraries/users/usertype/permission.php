<?php
namespace packages\userpanel\usertype;

use packages\base\{db, db\dbObject, Options};

class Permission extends dbObject {

	private static $disabledPermissions = null;

	public static function getDisabledPermissions(bool $userCache = true): array {
		if (static::$disabledPermissions === null or !$userCache) {
			static::$disabledPermissions = Options::get("packages.userpanel.disabledpermisions");
			if (!static::$disabledPermissions or !is_array(static::$disabledPermissions)) {
				static::$disabledPermissions = array();
			}
		}
		return static::$disabledPermissions;
	}

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
