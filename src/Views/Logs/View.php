<?php

namespace packages\userpanel\Views\Logs;

use packages\userpanel\Log;
use packages\userpanel\View as UserPanelView;

class View extends UserPanelView
{
    public function setLog(Log $log): void
    {
        $this->setData($log, 'log');
    }

    protected function getLog(): Log
    {
        return $this->getData('log');
    }
}
