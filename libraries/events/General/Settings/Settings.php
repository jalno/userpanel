<?php
namespace packages\userpanel\events\General;
use packages\base\event;
use packages\userpanel\events\General\Settings\Setting;

class Settings extends event {
	private $settings = [];
	public function addSetting(Setting $setting) {
		$this->settings[$setting->getName()] = $setting;
	}
	public function getSettingNames() {
		return array_keys($this->settings);
	}
	public function getByName($name) {
		return $this->settings[$name] ?? null;
	}
	public function get(){
		return $this->settings;
	}
}
