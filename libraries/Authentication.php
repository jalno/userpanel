<?php
namespace packages\userpanel;

use packages\base\{Response, http};
use packages\userpanel\{events\AuthenticationHandlersEvent, Authentication\IHandler};

class Authentication {

	/**
	 * @var User|null Authenticated User
	 */
	protected static $user;

	/**
	 * @var IHandler|null Authentication handler instance.
	 */
	protected static $handler;

	/**
	 * Setter for authenticated user.
	 * this method doesn't set the handler flags.
	 * 
	 * @param User|null $user
	 * @return void
	 */
	public static function setUser(?User $user): void {
		self::$user = $user;
	}

	/**
	 * Getter for authenticated user.
	 * 
	 * @return User|null
	 */
	public static function getUser(): ?User {
		return self::$user;
	}

	/**
	 * Get authenticated user's ID.
	 * 
	 * @return int|null
	 */
	public static function getID(): ?int {
		return self::$user ? self::$user->id : null;
	}

	/**
	 * Get the user's name.
	 * 
	 * @return string|null
	 */
	public static function getName(): ?string {
		return self::$user ? self::$user->name : null;
	}

	/**
	 * Get the authentication handler.
	 * 
	 * @return IHandler|null
	 */
	public static function getHandler(): ?IHandler {
		return self::$handler;
	}

	/**
	 * Set handler instance.
	 * 
	 * @param IHandler|null
	 * @return void
	 */
	public static function setHandler(?IHandler $handler): void {
		self::$handler = $handler;
	}

	/**
	 * Check user authentication.
	 * 
	 * @return bool
	 */
	public static function check(): bool {
		if (self::$handler) {
			self::$user = self::$handler->check();
			return self::$user !== null;
		}
		$event = new AuthenticationHandlersEvent();
		$event->trigger();
		$handlers = $event->getHandlers();
		foreach ($handlers as $handler) {
			$instance = new $handler();
			self::$user = $instance->check();
			if (self::$user) {
				self::$handler = $instance;
				return true;
			}
		}
		return false;
	}

	/**
	 * Forget identity of user and authentication handler.
	 * 
	 * @return void
	 */
	public static function forget(): void {
		if (self::$handler) {
			self::$handler->forget();
		}
		self::$user = null;
		self::$handler = null;
	}
	
	public static function FailResponse(){
		$response = new Response(false);
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
				$response->go(url('login', ['backTo' => http::$request['uri'] . (http::$request['get'] ? "?" . http_build_query(http::$request['get']): "")]));
			}else{
				$response->go(url('login', ['backTo' => http::getURL()]));
			}
			
		}
		if ($response->is_ajax() or $response->is_api()) {
			$response->setHttpCode(401);
		}
		return($response);
	}
	
}
