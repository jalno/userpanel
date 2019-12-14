<?php
namespace packages\userpanel\events;

use packages\base\Event;
use packages\userpanel\{resetpwd\Token, User};
use packages\notifications\notifiable;

class ResetPWD extends Event implements notifiable {

	private $token;

	public function __construct(Token $toekn) {
		$this->token = $token;
	}

	public static function getName(): string {
		return "userpanel_resetpwd_token";
	}

	public static function getParameters(): array {
		return [User::class];
	}

	public function getArguments(): array {
		return array(
			"token" => $this->token,
		);
	}

	public function getTargetUsers(): array {
		return array($this->token->user);
	}

}