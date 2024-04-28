<?php

namespace packages\userpanel\Events;

use packages\base\Event;

class Search extends Event
{
    public $word;

    public function __construct($word)
    {
        $this->word = $word;
    }
}
