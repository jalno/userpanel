<?php

namespace packages\userpanel\UserType;

use packages\base\Events;
use packages\base\Options;
use packages\userpanel\Events\usertype_permissions_list;
use packages\userpanel\User;

class Permissions
{
    protected static $permissions = [];
    private static $disabledPermissions;

    public static function add($permission)
    {
        if (!in_array($permission, self::$permissions)) {
            self::$permissions[] = $permission;
        }
    }

    public static function get()
    {
        Events::trigger(new usertype_permissions_list());

        return self::$permissions;
    }

    public static function has($permission)
    {
        return in_array($permission, self::$permissions);
    }

    public static function existentForUser(User $user, bool $withoutDisabled = true): array
    {
        $allPermissions = $user->isManager() ? static::get() : $user->getPermissions();
        if ($withoutDisabled) {
            $disabledPermissions = self::getDisabledPermissions();
            if ($disabledPermissions) {
                $allPermissions = array_diff($allPermissions, $disabledPermissions);
            }
        }

        return array_values($allPermissions);
    }

    public static function getDisabledPermissions(bool $useCache = true): array
    {
        if (null === self::$disabledPermissions or !$useCache) {
            self::$disabledPermissions = Options::get('packages.userpanel.disabledpermisions');
            if (!self::$disabledPermissions or !is_array(self::$disabledPermissions)) {
                self::$disabledPermissions = [];
            }
        }

        return self::$disabledPermissions;
    }
}
