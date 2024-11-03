<?php

namespace packages\userpanel\Events\Settings;

use packages\base\Exception;

class ControllerException extends Exception
{
    /**
     * @var string
     */
    private $controller;

    public function __construct(string $controller)
    {
        $this->controller = $controller;
    }

    public function getController(): string
    {
        return $this->controller;
    }
}