<?php

namespace packages\userpanel;

use packages\base\DB\DBObject;

class LogUser extends DBObject
{
    protected $dbTable = 'userpanel_logs_users';
    protected $primaryKey = '';
    protected $dbFields = [
        'log' => ['type' => 'int', 'required' => true],
        'user' => ['type' => 'int', 'required' => true],
    ];
    protected $relations = [
        'user' => ['hasOne', User::class, 'id'],
    ];
}
