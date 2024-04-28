<?php

namespace packages\userpanel\Views\Settings\UserTypes;

use packages\userpanel\UserType;
use packages\userpanel\Views\Form;

class Delete extends Form
{
    public function setUserType(UserType $usertype)
    {
        $this->setData($usertype, 'usertype');
    }

    public function getUserType()
    {
        return $this->getData('usertype');
    }
}
