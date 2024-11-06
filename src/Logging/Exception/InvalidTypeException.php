<?php

namespace packages\userpanel\Logging\Exception;

use packages\userpanel\Logging\Exception;

class InvalidTypeException extends Exception
{
    /**
     * @var string
     */
    private $type;

    public function __construct(string $type, string $message = '')
    {
        parent::__construct($message ?: 'Invalid log type: '.$type);
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
