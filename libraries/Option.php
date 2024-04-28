<?php

namespace packages\userpanel;

use packages\base\DB\DBObject;

class usertype_option extends DBObject
{
    protected $dbTable = 'userpanel_usertypes_options';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'usertype' => ['type' => 'int', 'required' => true],
        'name' => ['type' => 'text', 'required' => true],
        'value' => ['type' => 'text', 'required' => true],
    ];
    protected $relations = [
        'usertype' => ['hasOne', 'packages\\userpanel\\usertype', 'usertype'],
    ];
}
