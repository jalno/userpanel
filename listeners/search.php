<?php
namespace packages\userpanel\listeners;
use \packages\base\db\parenthesis;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\date;
use \packages\userpanel\user;
use \packages\userpanel\usertype;
use \packages\userpanel\authorization;
use \packages\userpanel\authentication;
use \packages\userpanel\events\search as event;
use \packages\userpanel\search as saerchHandler;
use \packages\userpanel\search\link;
class search{
	public function find(event $e){
		if(authorization::is_accessed('users_list') and authorization::is_accessed('users_view')){
			$this->users($e->word);
		}
		if(authorization::is_accessed('usertypes_list') and authorization::is_accessed('usertypes_edit')){
			$this->usertypes($e->word);
		}
		$this->profile($e->word);
	}
	public function users($word){
		$types = authorization::childrenTypes();
		$user = new user();
		if($types){
			$user->where("type", $types, 'in');
		}else{
			$user->where("id", authentication::getID());
		}
		$parenthesis = new parenthesis();
		foreach(array('name', 'lastname', 'email', 'cellphone') as $item){
			$parenthesis->where($item,$word, 'contains', 'OR');
		}
		$user->where($parenthesis);
		$users = $user->get();
		foreach($users as $user){
			$result = new link();
			$result->setLink(userpanel\url('users/view/'.$user->id));
			$result->setTitle(translator::trans("user.view.byFullName", array(
				'fullname' => $user->getFullName()
			)));
			$result->setDescription(translator::trans("search.user.description", array(
				'email' => $user->email,
				'cellphone' => $user->cellphone,
				'lastonline' => date::format("Y/m/d H:i", $user->lastonline),
				'type_name' => $user->type->title
			)));
			saerchHandler::addResult($result);
		}
	}
	public function profile($word){
		if(strpos($word, translator::trans('profile')) !== false){
			if(authorization::is_accessed('profile_view')){
				$result = new link();
				$result->setLink(userpanel\url('profile/view'));
				$result->setTitle(translator::trans("profile.view"));
				saerchHandler::addResult($result);
			}
			if(authorization::is_accessed('profile_edit')){
				$result = new link();
				$result->setLink(userpanel\url('profile/edit'));
				$result->setTitle(translator::trans("profile.edit"));
				saerchHandler::addResult($result);
			}
		}
	}
	public function usertypes($word){
		$types = authorization::childrenTypes();
		$usertype = new usertype();
		$usertype->where("id", $types, 'in');
		$parenthesis = new parenthesis();
		foreach(array('title') as $item){
			$parenthesis->where($item,$word, 'contains', 'OR');
		}
		$usertype->where($parenthesis);
		$usertypes = $usertype->get();
		foreach($usertypes as $usertype){
			$result = new link();
			$result->setLink(userpanel\url('settings/usertypes/edit/'.$usertype->id));
			$result->setTitle(translator::trans("usertype.edit.byTitle", array(
				'title' => $usertype->title
			)));
			saerchHandler::addResult($result);
		}
	}
}
