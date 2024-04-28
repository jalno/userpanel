<?php

namespace packages\userpanel\Views\Users;

use packages\userpanel\Authorization;
use packages\userpanel\Country;
use packages\userpanel\User\SocialNetwork;
use packages\userpanel\Views\Form;

class Edit extends Form
{
    use SettingsTrait;

    /** @var bool */
    protected $canEditPrivacy;

    /** @var bool */
    protected $canChangeCredit;

    /** @var bool that indicates viewer user can change permissions of the user */
    protected $canEditPermissions;

    /**
     * @var bool
     */
    protected $canEditPassword;

    public function __construct()
    {
        $this->canEditPrivacy = Authorization::is_accessed('profile_edit_privacy');
        $this->canChangeCredit = Authorization::is_accessed('users_edit_credit');
        $this->canEditPermissions = Authorization::is_accessed('users_edit_permissions');
        $this->canEditPassword = Authorization::is_accessed('users_edit_password');
    }

    public function setCountries($countries): void
    {
        $this->setData($countries, 'countries');
    }

    public function getCountries(): array
    {
        return $this->getData('countries');
    }

    public function setTypes($types): void
    {
        $this->setData($types, 'types');
    }

    public function getTypes(): array
    {
        return $this->getData('types');
    }

    public function setForm(): void
    {
        $user = $this->getData('user');
        $userArray = $user->toArray();
        $country = $user->country ? $user->country : Country::getDefaultCountry();
        foreach (['phone', 'cellphone'] as $field) {
            $item = explode('.', $userArray[$field]);
            $count = count($item);
            $userArray["{$field}[code]"] = $count > 1 ? $item[0] : $country->id;
            $userArray["{$field}[number]"] = $count > 1 ? $item[1] : $item[0];
            unset($userArray[$field]);
        }
        $this->setDataForm($userArray);
        foreach ($user->socialnetworks as $socialnet) {
            $this->setDataForm($socialnet->username, "socialnets[{$socialnet->network}]");
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
