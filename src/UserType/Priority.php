<?php

namespace packages\userpanel\UserType;

use packages\base\DB;
use packages\base\DB\DBObject;
use packages\userpanel\UserType;

class Priority extends DBObject
{
    protected $dbTable = 'userpanel_usertypes_priorities';
    protected $primaryKey = 'parent';
    public $returnType = 'Array';
    protected $dbFields = [
        'parent' => ['type' => 'int', 'required' => true],
        'child' => ['type' => 'int', 'required' => true],
    ];
    protected $relations = [
        'parentdata' => ['hasOne', UserType::class, 'parent'],
        'childdata' => ['hasOne', UserType::class, 'child'],
    ];

    public function delete()
    {
        DB::where('parent', $this->parent);
        DB::where('child', $this->child);

        return DB::delete($this->dbTable);
    }
}
