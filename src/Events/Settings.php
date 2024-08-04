<?php

namespace packages\userpanel\Events;

use packages\base\Event;
use packages\userpanel\Events\Settings\Tuning;
use packages\userpanel\User;

class Settings extends Event
{
    private $settings = [];
    private $user;

    public function addTuning(Tuning $tuning)
    {
        $this->settings[$tuning->getName()] = $tuning;
    }

    public function getTuningNames()
    {
        return array_keys($this->settings);
    }

    public function getByName($name)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : null;
    }

    public function get()
    {
        return $this->settings;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
