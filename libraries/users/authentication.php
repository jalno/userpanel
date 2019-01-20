<?php
namespace packages\userpanel;
use \packages\base;
use \packages\base\response;
use \packages\base\session;
use \packages\base\http;
use packages\userpanel\user\Api;
class authentication{
	static private $user;
	static private $api;
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
		self::$user = null;
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
		if (!session::status()) {
			session::start();
		}
		if (session::status()) {
			if (!self::$user) {
				if (($userid = session::get("userid")) > 0) {
					if (self::$user = user::where("id", $userid)->where("status", user::active)->getOne()) {
						return true;
					}
				}
			} else {
				return true;
			}
		}
		return false;
	}
	static public function check(){
		if (self::getSession()) {
			if(!session::get("lock")){
				return true;
			}
		} else {
			$header = http::getHeader("authorization");
			if ($header) {
				$header = explode(" ", $header, 2);
				if (count($header) == 2 and strtolower($header[0]) == "bearer") {
					$api = Api::where("token", $header[1])->where("status", Api::active)->getOne();
					if ($api) {
						self::$api = $api;
						self::$user = $api->user;
						return true;
					}
				}
			}
		}
		return false;
	}
	static public function FailResponse(){
		$response = new response(false);
		if(url() == http::$request['uri']){
			$response->go(url('login'));
		}else{
			$indexurl = parse_url(url('', [], true));
			if(!isset($indexurl['port'])){
				switch($indexurl['scheme']){
					case('http'):$indexurl['port'] = 80;break;
					case('https'):$indexurl['port'] = 443;break;
				}
			}
			if($indexurl['scheme'] == http::$request['scheme'] and $indexurl['host'] == http::$request['hostname'] and $indexurl['port'] == http::$server['port']){
				$response->go(url('login', ['backTo' => http::$request['uri']]));
			}else{
				$response->go(url('login', ['backTo' => http::getURL()]));
			}
			
		}
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
	public static function getApi() {
		return self::$api;
	}
	public static function setApi(Api $api) {
		self::$api = $api;
	}
}
