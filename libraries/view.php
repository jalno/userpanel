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

    public function output()
    {
        if ($user = Authentication::getUser()) {
            $user->lastonline = time();
            $user->save();
        }
        parent::output();
    }
}
class View extends \packages\base\View
{
    use ViewTrait;
}
