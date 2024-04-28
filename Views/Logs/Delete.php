<?php

namespace packages\userpanel\Views\Logs;

use packages\userpanel\Log;
use packages\userpanel\Views\Form;

class Delete extends Form
{
    public function setLog(Log $log)
    {
        $this->setData($log, 'log');
    }

    protected function getLog(): Log
    {
        return $this->getData('log');
    }
}
