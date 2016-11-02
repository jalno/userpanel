<?php
namespace packages\userpanel\views\tools;
use \packages\userpanel\views\listview as list_view;
use \packages\userpanel\authorization;
use \packages\base\views\traits\form as formTrait;
class usertype extends  list_view{
	use formTrait;
	protected $canEdit;
	protected $canDelete;
	protected $usertypes;
	static protected $navigation;
	function __construct(){
		$this->canEdit = authorization::is_accessed('usertype_edit');
		$this->canDelete = authorization::is_accessed('usertype_delete');
	}
	static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('usertype_list');
	}
	public function setUserTypes($usertypes){
		$this->usertypes = $usertypes;
	}
	public function getUserTypes(){
		return $this->usertypes;
	}
}
