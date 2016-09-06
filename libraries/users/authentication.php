<?php
namespace packages\userpanel;
use \packages\base;
use \packages\base\response;
use \packages\base\session;
use \packages\base\http;
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
			return true;
		}
		return false;
	}
	static function unsetSession(){
		if(session::status()){
			session::unsetval("userid");
		}
		return true;
	}
	static public function check(){
		if(!session::status()){
			session::start();
		}
		if(session::status()){
			if(($userid = session::get("userid")) > 0){
				self::$user = new user();
				if(self::$user->byId($userid)){
					return true;
				}
			}
		}
		return false;
	}
	static public function FailResponse(){
		$response = new response(false);
		$response->go(base\url('userpanel/login'));
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
