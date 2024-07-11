<?php

namespace packages\userpanel\Views\Settings\UserTypes;

use packages\userpanel\User;
use packages\userpanel\UserType;
use packages\userpanel\Views\Form;

class Edit extends Form
{
    public function setUserType(UserType $usertype)
    {
        $this->setData($usertype, 'usertype');
    }

    public function getUserType()
    {
        return $this->getData('usertype');
    }

    public function setChildrenTypes($usertypes)
    {
        $this->setData($usertypes, 'childrentypes');
    }

    public function getChildrenTypes()
    {
        return $this->getData('childrentypes');
    }

    public function setPermissions($permissions)
    {
        $this->setData($permissions, 'permissions');
    }

    public function getPermissions()
    {
        return $this->getData('permissions');
    }

    public function hasCustomizedPermissionsUser(): bool
    {
        $type = $this->getUserType();

        return (new User())->where('type', $type->id)->where('has_custom_permissions', true)->has();
    }
}
