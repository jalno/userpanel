<?php
namespace packages\userpanel;

use packages\base\db;
use packages\userpanel\Usertype\Permission;

class usertype extends db\dbObject{
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

    /**
     * @var string[]
     */
    private $plainPermissions;

	/**
	 * get permissions of this usertype with or without disabled permissions
	 *
	 * @param bool $withoutDisabledPermissions if true, disabled permissions are filtered from result
	 * @return string[] that is permissions of this type
	 */
	public function getPermissions(bool $withoutDisabledPermissions = true): array {
		$permissions = (new Permission)->where('type', $this->id)->get();
		if ($withoutDisabledPermissions) {
			$disabledPermissions = Permission::getDisabledPermissions();
			return array_values(array_filter($permissions, function($obj) use ($disabledPermissions) {
				return !in_array($obj->name, $disabledPermissions);
			}));
		}
		return $permissions;
	}

    /**
     * Load and cache permissions of this usertype for once and check it for every query.
     * 
     * @param string $permission permission name.
     * @return bool
     */
    public function hasPermission(string $permission): bool {
    	if ($this->plainPermissions === null) {
    		$this->plainPermissions = array_column($this->getPermissions(false), 'name');
    	}
    	return in_array($permission, $this->plainPermissions);
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
