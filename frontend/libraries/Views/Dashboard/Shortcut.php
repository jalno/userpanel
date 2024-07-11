<?php

namespace themes\clipone\Views\Dashboard;

class Shortcut
{
    public const Danger = 'danger';
    public const Warning = 'warning';
    public const Info = 'info';
    public const Success = 'success';
    public $name;
    public $icon;
    public $title;
    public $color;
    public $description;
    public $text;
    public $link;
    public $priority = 0;
    public $size = 4;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setLink($title, $link)
    {
        $this->link = [$title, $link];
    }
}
