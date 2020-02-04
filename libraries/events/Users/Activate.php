<?php
namespace packages\userpanel\events\Users;

use packages\base\Event;
use packages\userpanel\{User};
use packages\notifications\Notifiable;

class Activate extends Event implements Notifiable {
	private $user;
	public function __construct(User $user) {
		$this->user = $user;
	}
	public static function getName(): string {
		return "userpanel_users_activate";
	}
	public static function getParameters(): array {
		return [User::class];
	}
	public function getArguments(): array {
		return array(
			"user" => $this->user,
		);
	}
	public function getTargetUsers(): array {
		return array($this->user);
	}
}
