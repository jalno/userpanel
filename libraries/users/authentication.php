<?php
namespace packages\userpanel;
use \packages\base;
use \packages\base\response;
use \packages\base\session;
use \packages\base\http;
use \packages\userpanel\controllers\login;
class authentication{
	static private $user;
	static function setUser(user $user){
		if($user->id){
			self::$user = $user;
		}
		return false;
	}
	static function setSession(){
		if(self::$user){
			if(!session::status()){
				session::start();
			}
			session::set("userid", self::$user->id);
			$userid = session::get("userid");
			return true;
		}
		return false;
	}
	static function unsetSession(){
		session::start();
		if(session::status()){
			session::unsetval("userid");
			session::unsetval("lock");
		}
		return true;
	}
	static function lockSession(){
		if(self::getSession()){
			session::set("lock", true);
			return true;
		}
		return false;
	}
	static function unlockSession(){
		if(self::getSession()){
			session::unsetval("lock");
			return true;
		}
		return false;
	}
	static function getSession(){
		if(!session::status()){
			session::start();
		}
		if(session::status()){
			if(!self::$user){
				if(($userid = session::get("userid")) > 0){
					if(self::$user = user::byId($userid)){
						return true;
					}
				}
			}else{
				return true;
			}
		}
		return false;
	}
	static public function check(){
		if(self::getSession()){
			if(!session::get("lock")){
				return true;
			}
		}elseif(session::status()){
			if($user = login::checkRememberToken()){
				login::doLogin($user);
				return true;
			}
		}
		return false;
	}
	static public function FailResponse(){
		$response = new response(false);
		$response->go(url('login'));
		return($response);
	}
	static function getUser(){
		return self::$user;
	}
	static function getID(){
		return self::$user->id;
	}
	static function getName(){
		return self::$user->name;
	}
}
