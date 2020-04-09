<?php
namespace packages\userpanel\views\settings\usertypes;

use packages\userpanel\views\Form;
use packages\userpanel\Usertype;

class Add extends Form {
	public function setChildrenTypes($usertypes) {
		$this->setData($usertypes, 'childrentypes');
	}
	public function getChildrenTypes() {
		return $this->getData('childrentypes');
	}
	public function setPermissions($permissions) {
		$this->setData($permissions, 'permissions');
	}
	public function getPermissions() {
		return $this->getData('permissions');
	}
}
