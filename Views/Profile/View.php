<?php

namespace packages\userpanel\Views\Profile;

use packages\base\Options;
use packages\base\Packages;
use packages\userpanel\Authorization;

class View extends \packages\userpanel\View
{
    use SettingsTrait;

    public function setUserData($data)
    {
        $this->setData($data, 'user');
    }

    public function getUserData($key)
    {
        return $this->data['user']->$key;
    }

    public function canViewLog(): bool
    {
        return Authorization::is_accessed('logs_view');
    }

    public function export(): array
    {
        $user = $this->getData('user');
        $data = [
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'cellphone' => $user->cellphone,
                    'phone' => $user->phone,
                    'city' => $user->city,
                    'country' => $user->country ? $user->country->name : null,
                    'zip' => $user->zip,
                    'address' => $user->address,
                    'credit' => $user->credit,
                    'status' => $user->status,
                    'avatar' => $user->avatar,
                ],
                'permissions' => $user->getPermissions(),
            ],
        ];
        if (Packages::package('financial')) {
            $data['data']['user']['currency'] = \Packages\financial\currency::getDefault($user)->title;
        } else {
            $data['data']['user']['currency'] = Options::get('packages.userpanel.users.credit.currency.title');
        }

        return $data;
    }
}
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
