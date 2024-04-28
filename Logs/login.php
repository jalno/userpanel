<?php

namespace packages\userpanel\Logs;

use packages\base\View;
use packages\userpanel\Logs;

class Login extends Logs
{
    public static function isActivity()
    {
        return false;
    }

    public function getColor(): string
    {
        return 'circle-green';
    }

    public function getIcon(): string
    {
        return 'clip-key';
    }

    public function buildFrontend(View $view)
    {
    }
}
