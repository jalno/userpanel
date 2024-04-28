<?php

namespace packages\userpanel\User;

use packages\base\DB\DBObject;

class Option extends DBObject
{
    protected $dbTable = 'userpanel_users_options';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'user' => ['type' => 'int', 'required' => true],
        'name' => ['type' => 'text', 'required' => true],
        'value' => ['type' => 'text', 'required' => true],
    ];
    protected $relations = [
        'user' => ['hasOne', 'packages\\userpanel\\user', 'user'],
    ];
    protected $jsonFields = ['value'];
}
