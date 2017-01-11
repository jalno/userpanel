<?php
namespace packages\userpanel;
use packages\base\db;
use packages\base\db\dbObject;
use packages\base\utility\password;
use \packages\base\IO;
use \packages\base\packages;
class user extends dbObject{
	const active = 1;
	const deactive = 0;
	const suspend = 2;
	protected $dbTable = "userpanel_users";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'email' => array('type' => 'text', 'required' => true, 'unique' => 'true'),
        'name' => array('type' => 'text', 'required' => true),
        'lastname' => array('type' => 'text', 'required' => true),
        'cellphone' => array('type' => 'text', 'required' => true, 'unique' => 'true'),
        'password' => array('type' => 'text', 'required' => true),
		'type' => array('type' => 'int', 'required' => true),
		'phone' => array('type' => 'text'),
		'city' => array('type' => 'text'),
		'country' => array('type' => 'text'),
		'zip' => array('type' => 'int'),
		'address' => array('type' => 'text'),
		'web' => array('type' => 'text'),
		'avatar' => array('type' => 'text'),
		'credit' => array('type' => 'int'),
		'lastonline' => array('type' => 'int'),
        'status' => array('type' => 'int', 'required' => true)
    );
    protected $relations = array(
        'type' => array("hasOne", "packages\\userpanel\\usertype", "type"),
		'socialnetworks' => array("hasMany", "packages\\userpanel\\user\\socialnetwork", "user"),
		'options' => array("hasMany", "packages\\userpanel\\user_option", "user"),
		'country' => array("hasOne", "packages\\userpanel\\country", "country"),
    );
	public function getFullName(){
		return($this->name.($this->lastname ? ' '.$this->lastname : ''));
	}
	public function password_verify($password){
		return(password::verify($password, $this->password));
	}
	public function password_hash($password){
		$this->password = password::hash($password);
	}
	public function can($permission){
		return $this->type->hasPermission($permission);
	}
	public function childrenTypes(){
		$children = array();
		$types = $this->type->children;
		if($types){
			foreach($types as $type){
				$children[] = $type->child;
			}
		}
		return $children;
	}
	public function option($name){
		foreach($this->options as $option){
			if($option->name == $name){
				return $option->value;
			}
		}
		return $this->type->option($name);
	}
	public function save($data = null){
		$oldavatar = null;
		if($this->avatar){
			$oldavatar = $this->avatar;
		}
		parent::save($data);
		if($oldavatar and $oldavatar != $this->avatar){
			db::where("avatar", $oldavatar);
			if(!db::has($this->dbTable)){
				IO\unlink(packages::package('userpanel')->getFilePath($oldavatar));
			}
		}

	}
	public function delete(){
		parent::delete();
		db::where("avatar", $this->avatar);
		if(!db::has($this->dbTable)){
			IO\unlink(packages::package('userpanel')->getFilePath($this->avatar));
		}
	}
	public function toArray($recursive = false){
		$password = $this->password;
		unset($this->data['password']);
		$return = parent::toArray($recursive);

		$this->password = $password;
		return $return;
	}
}
