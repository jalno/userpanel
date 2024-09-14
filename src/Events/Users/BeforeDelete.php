<?php

namespace packages\userpanel\Events\Users;

use packages\base\Event;
use packages\base\Exception;
use packages\base\View\Error;
use packages\userpanel\{User};

class BeforeDelete extends Event
{
    private $user;

    protected $errorsByType = [];

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errorsByType);
    }

    public function addError(Error $error): void
    {
        $type = $error->getType();
        if (!isset($this->errorsByType[$type])) {
            $this->errorsByType[$type] = [];
        }
        $this->errorsByType[$type][] = $error;
    }

    public function getErrors(): array
    {
        $allErrors = [];
        foreach ($this->errorsByType as $type => $errors) {
            $allErrors = array_merge($allErrors, $errors);
        }

        return $allErrors;
    }

    public function getErrorsByType(string $type): array
    {
        if (!in_array($type, [Error::SUCCESS, Error::WARNING, Error::FATAL, Error::NOTICE])) {
            throw new Exception('type');
        }

        return $this->errorsByType[$type] ?? [];
    }

    public function getErrorByCode(string $code): ?Error
    {
        foreach ($this->getErrors() as $error) {
            if ($error->code == $code) {
                return $error;
            }
        }

        return null;
    }
}
