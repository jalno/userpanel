<?php
namespace packages\userpanel\views\settings\usertypes;
use \packages\userpanel\views\listview as list_view;
use \packages\userpanel\authorization;
use \packages\base\views\traits\form as formTrait;
class listview extends  list_view{
	use formTrait;
	protected $canAdd;
	protected $canEdit;
	protected $canDelete;
	protected $usertypes;
	static protected $navigation;
	function __construct(){
		$this->canAdd = authorization::is_accessed('settings_usertypes_add');
		$this->canEdit = authorization::is_accessed('settings_usertypes_edit');
		$this->canDelete = authorization::is_accessed('settings_usertypes_delete');
	}
	static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('settings_usertypes_list');
	}
	public function getUserTypes(){
		return $this->dataList;
	}
}
