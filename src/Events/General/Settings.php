<?php

namespace packages\userpanel\Events\General;

use packages\base\Event;
use packages\userpanel\Events\General\Settings\Setting;

class Settings extends Event
{
    private $settings = [];

    public function addSetting(Setting $setting)
    {
        $this->settings[$setting->getName()] = $setting;
    }

    public function getSettingNames()
    {
        return array_keys($this->settings);
    }

    public function getByName($name)
    {
        return $this->settings[$name] ?? null;
    }

    public function get()
    {
        return $this->settings;
    }
}
