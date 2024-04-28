<?php

namespace packages\userpanel\Events\Settings;

use packages\base\Exception;

class InputNameException extends Exception
{
    /**
     * @var string
     */
    private $input;

    public function __construct(string $input)
    {
        $this->input = $input;
    }

    public function getInput(): string
    {
        return $this->input;
    }
}
