<?php

namespace packages\userpanel\Views;

use packages\userpanel\View;

class ErrorView extends View
{
    protected $errorcode;
    protected $errortext;

    public function setErrorCode($code)
    {
        $this->errorcode = $code;
    }

    public function setErrorText($text)
    {
        $this->errortext = $text;
    }
}
