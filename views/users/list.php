<?php
namespace packages\userpanel\views\users;
use \packages\userpanel\views\listview as list_view;
use \packages\userpanel\authorization;
use \packages\base\views\traits\form as formTrait;
class listview extends  list_view{
	use formTrait;
	protected $canView;
	protected $canEdit;
	protected $canDelete;
	protected $canExport;
	static protected $navigation;
	function __construct(){
		$this->canView = authorization::is_accessed('users_view');
		$this->canEdit = authorization::is_accessed('users_edit');
		$this->canDelete = authorization::is_accessed('users_delete');
		$this->canExport = Authorization::is_accessed("users_export");
	}
	static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('users_list');
	}
	public function setUserTypes($types){
		$this->setData($types, 'usertypes');
	}
	public function getUserTypes(){
		return $this->getData('usertypes');
	}
}
