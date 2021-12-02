<?php
namespace packages\userpanel;

use packages\base\{db, db\dbObject, IO, Packages, utility\Password, utility};
use packages\base\Validator\{CellphoneValidator, Geo\CountryCodeToRegionCodeMap};
use packages\userpanel\{user\Option};

/**
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string|null $lastname
 * @property string $cellphone
 * @property string $password
 * @property Usertype|int $type
 * @property string|null $phone
 * @property string|null $city
 * @property Country|int $country
 * @property int $zip
 * @property string|null $address
 * @property string|null $web
 * @property double $credit
 * @property int $lastonline
 * @property string|null $remember_token
 * @property int $registered_at
 * @property bool $has_custom_permissions
 * @property int $status
 * @property User\Socialnetwork[] $socialnetworks
 * @property User\Option[] $options
 */
class user extends dbObject{
	use imageTrait;

	const active = 1;
	const deactive = 3;
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
		'registered_at' => array('type' => 'int', 'required' => true),
		'has_custom_permissions' => array('type' => 'int'),
        'status' => array('type' => 'int', 'required' => true)
    );
    protected $relations = array(
        'type' => array("hasOne", "packages\\userpanel\\usertype", "type"),
		'socialnetworks' => array("hasMany", "packages\\userpanel\\user\\socialnetwork", "user"),
		'options' => array("hasMany", "packages\\userpanel\\user\\option", "user"),
		'country' => array("hasOne", "packages\\userpanel\\country", "country"),
	);

	private $customPermissions = null;

	public function getFullName(){
		return($this->name.($this->lastname ? ' '.$this->lastname : ''));
	}
	public function password_verify($password){
		return(password::verify($password, $this->password));
	}
	public function password_hash($password){
		$this->password = password::hash($password);
	}
	public function setCustomPermissions(array $permissions): void {
		$typePermissions = array_column($this->type->permissions, "name");
		/* if given permissions is not diffrent than type permissions, no need to use custom permissions! */
		if (empty(array_diff($typePermissions, $permissions)) and empty(array_diff($permissions, $typePermissions))) {
			$this->has_custom_permissions = false;
			$this->save();
			$this->customPermissions = array();
			db::where("user_id", $this->id)->delete("userpanel_users_permissions");
			return;
		}
		$customPermissions = $this->has_custom_permissions ? $this->getCustomPermissions(false) : array();

		$removedPermissions = array_diff($customPermissions, $permissions);
		if ($removedPermissions) {
			$removedPermissions = array_values($removedPermissions);
			db::where("user_id", $this->id)->where("permission", $removedPermissions, "IN")->delete("userpanel_users_permissions");
		}

		$newPermissions = array_diff($permissions, $customPermissions);
		if ($newPermissions) {
			$userID = $this->id;
			db::insertMulti("userpanel_users_permissions", array_map(function ($permission) use ($userID) {
				return array(
					"user_id" => $userID,
					"permission" => $permission,
				);
			}, array_values($newPermissions)));
		}
		$this->has_custom_permissions = true;
		$this->save();
		$this->customPermissions = $permissions;
	}
	public function getCustomPermissions(bool $useCache = true): array {
		if ($this->customPermissions === null or !$useCache) {
			$this->customPermissions = array_column(db::where("user_id", $this->id)->get("userpanel_users_permissions", null, "permission"),
				"permission"
			);
		}
		return $this->customPermissions;
	}
	public function can(string $permission): bool {
		if ($this->has_custom_permissions) {
			return in_array($permission, $this->getCustomPermissions());
		}
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
	public function isManager(): bool {
		$parents = $this->parentTypes();
		if (empty($parents)) {
			return true;
		}
		return count($parents) == 1 and $parents[0] == $this->type->id;
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
		if (!parent::delete()) {
			return false;
		}
		if ($this->avatar) {
			db::where("avatar", $this->avatar);
			if(!db::has($this->dbTable)){
				Packages::package('userpanel')->getFile($this->avatar)->delete();
			}
		}
	}
	public function toArray($recursive = false){
		$password = $this->password;
		unset($this->data['password']);
		$remember_token = $this->remember_token;
		unset($this->data["remember_token"]);
		$return = parent::toArray($recursive);
		$this->password = $password;
		$this->remember_token = $remember_token;
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
	public function getPermissions(): array {
		if ($this->has_custom_permissions) {
			return $this->getCustomPermissions();
		}
		$typeID = $this->type->id;
		$permissions = (new usertype\Permission)->where("type", $typeID)->arrayBuilder()->get(null, "name");
		return array_column($permissions, "name");
	}
	public function preLoad(array $data): array {
		if (!isset($data["lastonline"])) {
			$data["lastonline"] = date::time();
		}
		if (!isset($data["registered_at"])) {
			$data["registered_at"] = date::time();
		}
		return $data;
	}
	public function getCellphoneWithDialingCode(): string {
		return utility\getTelephoneWithDialingCode($this->cellphone);
	}
	public function getPhoneWithDialingCode(): string {
		return $this->phone ? utility\getTelephoneWithDialingCode($this->phone) : "";
	}
}
