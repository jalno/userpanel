<?php

namespace packages\userpanel\Views;

use packages\userpanel\User;

class Lock extends Form
{
    public function setUser(User $user)
    {
        $this->setData($user, 'user');
    }

    public function getUser()
    {
        return $this->getData('user');
    }

    public function getUserData($key)
    {
        return $this->getUser()->$key;
    }
}
