<?php

namespace packages\userpanel\Views\Profile;

use packages\userpanel\Authorization;

trait SettingsTrait
{
	public function canEditSettings(): bool
	{
		return Authorization::is_accessed('profile_settings');
	}

	public function setSettings(array $settings)
	{
		$this->setData($settings, 'settings');
	}

	protected function getSettings(): array
	{
		return $this->getData('settings');
	}
}
