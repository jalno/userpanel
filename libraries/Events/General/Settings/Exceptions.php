<?php

namespace packages\userpanel\Events\General\Settings;

use packages\base\Exception;

class ControllerException extends Exception
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }
}
class InputNameRequiredException extends Exception
{
    private $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function getController()
    {
        return $this->input;
    }
}
