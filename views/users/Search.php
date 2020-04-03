<?php
namespace packages\userpanel\views\users;

use packages\base\views\traits\Form;
use packages\userpanel\{Authorization, views\listview};

class Search extends listview {

	public static function onSourceLoad(){
		self::$navigation = Authorization::is_accessed("users_list");
	}

	static protected $navigation;

	
	use Form;

	protected $canView;

	protected $canEdit;

	protected $canDelete;

	public function __construct() {
		$this->canView = Authorization::is_accessed("users_view");
		$this->canEdit = Authorization::is_accessed("users_edit");
		$this->canDelete = Authorization::is_accessed("users_delete");
	}
	
	public function setUserTypes(?array $types): void {
		$this->setData($types, "usertypes");
	}

	public function getUserTypes(): ?array {
		return $this->getData("usertypes");
	}

	public function setCountries(array $countries): void {
		$this->setData($countries, "countries");
	}

	public function getCountries(): ?array {
		return $this->getData("countries");
	}
}
