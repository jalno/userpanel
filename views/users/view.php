<?php
namespace packages\userpanel\views\users;
use \packages\base\packages;
use \packages\userpanel\authorization;
class view extends \packages\userpanel\view{
	protected $canEdit;
	function __construct(){
		$this->canEdit = authorization::is_accessed('users_edit');
		$this->canViewInvisibles = authorization::is_accessed('users_view_invisibles');
	}
	public function setUserData($data){
		$this->setData($data, 'user');
	}
	public function getUserData($key){
		return($this->data['user']->$key);
	}
}
