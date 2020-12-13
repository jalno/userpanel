<?php
namespace packages\userpanel\events\Users;

use packages\base\{view\Error, Event, Exception};
use packages\userpanel\{User};

class BeforeDelete extends Event {
	private $user;

	protected $errorsByType = array();

	public function __construct(User $user) {
		$this->user = $user;
	}
	public function getUser(): User {
		return $this->user;
	}
	public function hasErrors(): bool {
		return !empty($this->errorsByType);
	}
	public function addError(Error $error): void {
		$type = $error->getType();
		if (!isset($this->errorsByType[$type])) {
			$this->errorsByType[$type] = array();
		}
		$this->errorsByType[$type][] = $error;
	}
	public function getErrors(): array {
		$allErrors = array();
		foreach ($this->errorsByType as $type => $errors) {
			$allErrors = array_merge($allErrors, $errors);
		}
		return $allErrors;
	}
	public function getErrorsByType(string $type): array {
		if (!in_array($type, array(Error::SUCCESS, Error::WARNING, Error::FATAL, Error::NOTICE))) {
			throw new Exception("type");
		}
		return $this->errorsByType[$type] ?? [];
	}
	public function getErrorByCode(string $code): ?Error {
		foreach ($this->getErrors() as $error) {
			if ($error->code == $code) {
				return $error;
			}
		}
		return null;
	}
}
