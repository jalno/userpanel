<?php

namespace packages\userpanel\Views\Users;

use packages\userpanel\Authorization;

trait SettingsTrait
{
	public function canEditSettings(): bool
	{
		return Authorization::is_accessed('users_settings');
	}

	public function setSettings(?array $settings): void
	{
		$this->setData($settings, 'settings');
	}

	protected function getSettings(): ?array
	{
		return $this->getData('settings');
	}
}
