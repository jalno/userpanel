<?php
namespace packages\userpanel\events;

use packages\base\Event;
use packages\userpanel\{resetpwd\Token, User};
use packages\notifications\Notifiable;

class ResetPWD extends Event implements Notifiable {

	private $token;

	public function __construct(Token $token) {
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