<?php
namespace packages\userpanel\listeners;

use packages\userpanel\events\AuthenticationHandlersEvent as Event;
use packages\userpanel\Authentication\SessionHandler;

class AuthenticationHandlers {

	/**
	 * Add built-in authentication method to the list.
	 * 
	 * @param AuthenticationHandlersEvent $e
	 * @return void
	 */
	public function add(Event $e) {
		$e->addHandler(SessionHandler::class);
	}
}