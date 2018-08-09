<?php
namespace packages\userpanel;
use packages\base\db;
use packages\base\db\dbObject;
use packages\base\utility\password;
use \packages\base\IO;
use \packages\base\packages;
use \packages\userpanel\date;
use \packages\userpanel\user\option;

class user extends dbObject{
	use imageTrait;
	const active = 1;
	const deactive = 0;
	const suspend = 2;
	const onlineTimeout = 20;
	protected $dbTable = "userpanel_users";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'email' => array('type' => 'text', 'required' => true, 'unique' => 'true'),
        'name' => array('type' => 'text', 'required' => true),
        'lastname' => array('type' => 'text'),
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
		'credit' => array('type' => 'double'),
		'lastonline' => array('type' => 'int'),
		'remember_token' => array('type' => 'text'),
        'status' => array('type' => 'int', 'required' => true)
    );
    protected $relations = array(
        'type' => array("hasOne", "packages\\userpanel\\usertype", "type"),
		'socialnetworks' => array("hasMany", "packages\\userpanel\\user\\socialnetwork", "user"),
		'options' => array("hasMany", "packages\\userpanel\\user\\option", "user"),
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
	public function parentTypes(): array{
		$type = $this->type->id;
		db::where("child", $type);
		return array_column(db::get("userpanel_usertypes_priorities", null, ['parent']), 'parent');
	}
	public function option($name, $value = null){
		if($value){
			return $this->setOption($name, $value);
		}else{
			return $this->getOption($name);
		}
	}
	public function getOption($name){
		foreach($this->options as $option){
			if($option->name == $name){
				return $option->value;
			}
		}
		return $this->type->option($name);
	}
	public function setOption($name, $value){
		foreach($this->options as $option){
			if($option->name == $name){
				$option->value = $value;
				return $option->save();
			}
		}
		$option = new option();
		$option->user = $this->id;
		$option->name = $name;
		$option->value = $value;
		return $option->save();
	}
	public function getVisibility($field){
		$visibilities = $this->getOption("visibilities");
		return (is_array($visibilities) and in_array($field, $visibilities));
	}
	public function setVisibility($field, $visible){
		$visibilities = $this->getOption("visibilities");
		if(!is_array($visibilities)){
			$visibilities = array();
		}
		if($visible){
			$visibilities[] = $field;
		}elseif(($key = array_search($field, $visibilities)) !== false){
			unset($visibilities[$key]);
		}
		$visibilities = array_values(array_unique($visibilities));
		$this->setOption("visibilities", $visibilities);
	}

	public function save($data = null){
		$oldavatar = null;
		if($this->avatar){
			$oldavatar = $this->avatar;
		}
		if($id = parent::save($data)){
			if($oldavatar and $oldavatar != $this->avatar){
				db::where("avatar", $oldavatar);
				if(!db::has($this->dbTable)){
					IO\unlink(packages::package('userpanel')->getFilePath($oldavatar));
				}
			}
		}
		return $id;
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
	public function createRememberToken():string{
		$letters = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
		$rememberToken = "";
		for($i = 0; $i < 32; $i++){
			$rememberToken .= $letters[rand(0, 61)];
		}
		$this->remember_token = $rememberToken;
		$this->save();
		return $rememberToken;
	}
	public function online(){
		$this->lastonline = date::time();
		return $this->save();
	}
	public function isOnline():bool{
		return date::time() - $this->lastonline < self::onlineTimeout;
	}
    public function getAvatar(int $width, int $height) {
        return $this->getImage($width, $height, "avatar");
    }
}
