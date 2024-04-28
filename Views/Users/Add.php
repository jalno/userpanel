<?php

namespace packages\userpanel\Views\Users;

use packages\userpanel\Authorization;
use packages\userpanel\Country;
use packages\userpanel\UserType;
use packages\userpanel\Views\Form;

class Add extends Form
{
    /** @var bool */
    protected $canEditPrivacy;

    /** @var bool */
    protected $canChangeCredit;

    public function __construct()
    {
        $this->canChangeCredit = Authorization::is_accessed('users_edit_credit');
        $this->canEditPrivacy = Authorization::is_accessed('profile_edit_privacy');
    }

    /**
     * @param Country[] $countries
     */
    public function setCountries(array $countries): void
    {
        $this->setData($countries, 'countries');
    }

    /**
     * @return Country[]
     */
    public function getCountries(): array
    {
        return $this->getData('countries') ?? [];
    }

    /**
     * @param Usertype[] $types
     */
    public function setTypes(array $types): void
    {
        $this->setData($types, 'types');
    }

    /**
     * @return Usertype[]
     */
    public function getTypes(): array
    {
        return $this->getData('types') ?? [];
    }
}
