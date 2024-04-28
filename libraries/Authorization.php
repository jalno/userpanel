<?php

namespace packages\userpanel;

use packages\base\Options;

class Authorization
{
    public static function is_accessed($permission, $prefix = 'userpanel')
    {
        if ($prefix) {
            $prefix .= '_';
        }
        $user = Authentication::getUser();
        if ($user) {
            return $user->can($prefix.$permission);
        } else {
            $guestTypeID = Options::get('packages.userpanel.usertypes.guest');
            if ($guestTypeID) {
                $type = (new UserType())->byId($guestTypeID);
                if ($type) {
                    return $type->hasPermission($prefix.$permission);
                }
            }
        }

        return false;
    }

    public static function haveOrFail($permission, $prefix = 'userpanel')
    {
        if (!self::is_accessed($permission, $prefix)) {
            throw new AuthorizationException($prefix.$permission);
        }
    }

    public static function childrenTypes()
    {
        return Authentication::getUser()->childrenTypes();
    }
}
