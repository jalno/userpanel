<?php
namespace packages\userpanel\views\settings\bankaccount;
use \packages\userpanel\views\listview as list_view;
use \packages\userpanel\authorization;
use \packages\base\views\traits\form as formTrait;
class listview extends  list_view{
	use formTrait;
	protected $canEdit;
	protected $canDelete;
	protected $usertypes;
	static protected $navigation;
	function __construct(){
		$this->canEdit = authorization::is_accessed('settings_bankaccounts_edit');
		$this->canDelete = authorization::is_accessed('settings_bankaccounts_delete');
	}
	static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('settings_bankaccounts_list');
	}
	public function getBankaccounts(){
		return $this->dataList;
	}
}
