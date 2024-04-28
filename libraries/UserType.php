<?php

namespace packages\userpanel;

use packages\base\DB;
use packages\userpanel\UserType\Permission;
use packages\userpanel\UserType\Permissions;

class UserType extends DB\DBObject
{
    public const admin = 1;
    public const support = 2;
    public const guest = 3;
    protected $dbTable = 'userpanel_usertypes';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'title' => ['type' => 'text', 'required' => true],
    ];
    protected $relations = [
        'permissions' => ['hasMany', "packages\\userpanel\\usertype\permission", 'type'],
        'children' => ['hasMany', "packages\\userpanel\\usertype\priority", 'parent'],
        'options' => ['hasMany', 'packages\\userpanel\\usertype_option', 'usertype'],
    ];

    /**
     * @var string[]
     */
    private $plainPermissions;

    /**
     * get permissions of this usertype with or without disabled permissions.
     *
     * @param bool $withoutDisabledPermissions if true, disabled permissions are filtered from result
     *
     * @return string[] that is permissions of this type
     */
    public function getPermissions(bool $withoutDisabledPermissions = true): array
    {
        $permissions = (new Permission())->where('type', $this->id)->get();
        if ($withoutDisabledPermissions) {
            $disabledPermissions = Permissions::getDisabledPermissions();

            return array_values(array_filter($permissions, function ($obj) use ($disabledPermissions) {
                return !in_array($obj->name, $disabledPermissions);
            }));
        }

        return $permissions;
    }

    /**
     * Load and cache permissions of this usertype for once and check it for every query.
     *
     * @param string $permission permission name
     */
    public function hasPermission(string $permission): bool
    {
        if (null === $this->plainPermissions) {
            $this->plainPermissions = array_column($this->getPermissions(false), 'name');
        }

        return in_array($permission, $this->plainPermissions);
    }

    public function option($name)
    {
        foreach ($this->options as $option) {
            if ($option->name == $name) {
                return $option->value;
            }
        }

        return null;
    }
}
