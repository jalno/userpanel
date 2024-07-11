<?php

namespace themes\clipone\Views\Dashboard;

class Box
{
    public $name;
    public $icon;
    public $priority = 0;
    public $html = '';
    public $size = 12;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setHTML($html)
    {
        $this->html = $html;
    }

    public function getHTML()
    {
        return $this->html;
    }
}
