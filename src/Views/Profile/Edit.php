<?php

namespace packages\userpanel\Views\Profile;

use packages\base\{Validator\CellphoneValidator};
use packages\userpanel\Authorization;
use packages\userpanel\User\SocialNetwork;
use packages\userpanel\Views\Form;

class Edit extends Form
{
    use SettingsTrait;
    protected $canEditPrivacy;

    public function __construct()
    {
        $this->canEditPrivacy = Authorization::is_accessed('profile_edit_privacy');
    }

    public function setCountries($countries)
    {
        $this->setData($countries, 'countries');
    }

    public function getCountries()
    {
        return $this->getData('countries');
    }

    public function setUserData($data)
    {
        $this->setData($data, 'user');
    }

    public function getUserData($key)
    {
        return $this->data['user']->$key;
    }

    public function setForm()
    {
        $user = $this->getData('user');
        $userArray = $user->toArray();
        $defaultCountryCode = CellphoneValidator::getDefaultCountryCode();
        foreach (['phone', 'cellphone'] as $field) {
            if (strpos($userArray[$field], '.')) {
                $item = explode('.', $userArray[$field]);
                $userArray["{$field}[code]"] = $item[0] ?: $defaultCountryCode;
                $userArray["{$field}[number]"] = $item[1];
            } else {
                $userArray["{$field}[code]"] = $defaultCountryCode;
                $userArray["{$field}[number]"] = $userArray[$field];
            }
        }
        $this->setDataForm($userArray);
        foreach ($user->socialnetworks as $socialnet) {
            $this->setDataForm($socialnet->username, 'socialnets['.$socialnet->network.']');
        }
        foreach ([
            'email',
            'cellphone',
            'phone',
            'socialnetworks_'.SocialNetwork::telegram,
            'socialnetworks_'.SocialNetwork::instagram,
            'socialnetworks_'.SocialNetwork::skype,
            'socialnetworks_'.SocialNetwork::twitter,
            'socialnetworks_'.SocialNetwork::facebook,
            'socialnetworks_'.SocialNetwork::gplus,
        ] as $field) {
            $this->setDataForm($user->getVisibility($field), 'visibility_'.$field);
        }
    }
}
