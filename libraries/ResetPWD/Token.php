<?php

namespace packages\userpanel\ResetPWD;

use packages\base\DB\DBObject;
use packages\userpanel\User;

class Token extends DBObject
{
    protected $dbTable = 'userpanel_resetpwd_token';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'sent_at' => ['type' => 'int', 'required' => true],
        'user' => ['type' => 'int', 'required' => true],
        'token' => ['type' => 'text', 'required' => true],
        'ip' => ['type' => 'text', 'required' => true],
    ];
    protected $relations = [
        'user' => ['hasOne', User::class, 'user'],
    ];
}
