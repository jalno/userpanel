<?php

namespace packages\userpanel\Events\Users;

use packages\base\Event;
use packages\notifications\Notifiable;
use packages\userpanel\{User};

class Suspend extends Event implements Notifiable
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public static function getName(): string
    {
        return 'userpanel_users_suspend';
    }

    public static function getParameters(): array
    {
        return [User::class];
    }

    public function getArguments(): array
    {
        return [
            'user' => $this->user,
        ];
    }

    public function getTargetUsers(): array
    {
        return [$this->user];
    }
}
