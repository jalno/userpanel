<?php

namespace packages\userpanel\Events\Users;

use packages\base\Event;
use packages\userpanel\User;

class AfterDelete extends Event
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
