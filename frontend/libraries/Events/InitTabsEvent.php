<?php

namespace themes\clipone\Events;

use packages\base\Event;
use packages\base\View;

class InitTabsEvent extends Event
{
    /**
     * @var View
     */
    protected $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function getView(): View
    {
        return $this->view;
    }
}
