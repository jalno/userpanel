<?php
namespace packages\userpanel\events\Users;

use packages\base\Event;
use packages\userpanel\User;
use packages\notifications\Notifiable;

class AfterDelete extends Event {

	public function __construct(User $user) {
		$this->user = $user;
	}
	public function getUser(): User {
		return $this->user;
	}
}
