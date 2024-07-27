<?php

namespace packages\userpanel;

trait ViewTrait
{
    protected $shortdescription;

    public function setShortDescription($description)
    {
        $this->shortdescription = $description;
    }

    public function getShortDescription()
    {
        return $this->shortdescription;
    }

    public function output(): string
    {
        if ($user = Authentication::getUser()) {
            $user->lastonline = time();
            $user->save();
        }
        return parent::output();
    }
}
