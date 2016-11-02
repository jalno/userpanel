<?php
namespace packages\userpanel\views\tools;
use \packages\userpanel\views\listview as list_view;
use \packages\userpanel\authorization;
use \packages\userpanel\usertype;
use \packages\base\views\traits\form as formTrait;
class usertype_edit extends list_view{
	use formTrait;
	protected $usertype;
	protected $usertypes;
	protected $canPermissionEdit;
	protected $canPriorityEdit;
	static protected $navigation;
	function __construct(){
		$this->canPermissionEdit = authorization::is_accessed('permission_edit');
		$this->canPriorityEdit = authorization::is_accessed('priority_edit');
	}
	static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('usertype_edit');
	}
	public function setUserType(usertype $usertype){
		$this->usertype = $usertype;
	}
	public function getUserType(){
		return $this->usertype;
	}
	public function setUserTypes($usertypes){
		$this->usertypes = $usertypes;
	}
	public function getUserTypes(){
		return $this->usertypes;
	}
}
