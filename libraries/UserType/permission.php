<?php

namespace packages\userpanel\UserType;

use packages\base\DB;
use packages\base\DB\DBObject;

class Permission extends DBObject
{
    protected $dbTable = 'userpanel_usertypes_permissions';
    protected $primaryKey = '';
    protected $dbFields = [
        'type' => ['type' => 'int', 'required' => true],
        'name' => ['type' => 'text', 'required' => true],
    ];
    protected $relations = [
        'type' => ['hasOne', 'packages\\userpanel\\usertype', 'type'],
    ];

    public function delete()
    {
        DB::where('type', $this->data['type']);
        DB::where('name', $this->name);

        return DB::delete($this->dbTable);
    }
}
