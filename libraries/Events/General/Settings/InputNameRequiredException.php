<?php

namespace packages\userpanel\Events\General\Settings;

use packages\base\Exception;

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
