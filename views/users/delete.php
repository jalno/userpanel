<?php
namespace packages\userpanel\views\users;

use packages\userpanel\{views\Form, User};

class Delete extends Form {


	public function setUser(User $user): void {
		$this->setData($user, "user");
	}
	public function getUser(): User {
		return $this->getData("user");
	}
	public function setHasFatalError(bool $hasFatalError): void {
		$this->setData($hasFatalError, "hasFatalError");
	}
	public function hasFatalError(): bool {
		return $this->getData("hasFatalError") ?? false;
	}
}
