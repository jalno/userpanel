<?php

namespace packages\userpanel\Views\Users;

use packages\userpanel\User;
use packages\userpanel\Views\Form;

class Settings extends Form
{
    public function setUser(User $user)
    {
        $this->setData($user, 'user');
    }

    protected function getUser(): User
    {
        return $this->getData('user');
    }

    public function setSettings(array $settings)
    {
        $this->setData($settings, 'settings');
        foreach ($settings as $tunig) {
            foreach ($tunig->getDataForm() as $name => $value) {
                $this->setDataForm($value, $name);
            }
        }
    }

    protected function getSettings(): array
    {
        return $this->getData('settings');
    }
}
