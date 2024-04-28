<?php

namespace packages\userpanel;

use packages\base\DB\DBObject;

class log_param extends DBObject
{
    protected $dbTable = 'userpanel_logs_params';
    protected $primaryKey = '';
    protected $dbFields = [
        'log' => ['type' => 'int', 'required' => true],
        'name' => ['type' => 'text', 'required' => true],
        'value' => ['type' => 'text', 'required' => true],
    ];
    protected $jsonFields = ['value'];
}
