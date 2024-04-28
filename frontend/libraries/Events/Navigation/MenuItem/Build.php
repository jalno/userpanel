<?php

namespace themes\clipone\Events\Navigation\MenuItem;

use packages\base\Event;
use themes\clipone\Navigation\MenuItem;

class Build extends Event
{
    protected $item;

    public function __construct(MenuItem $item)
    {
        $this->item = $item;
    }

    public function getItem()
    {
        return $this->item;
    }
}
