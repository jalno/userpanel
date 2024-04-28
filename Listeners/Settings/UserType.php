<?php

namespace packages\userpanel\Listeners\Settings;

use packages\userpanel\UserType\Permissions;

class UserType
{
    public function permissions_list()
    {
        $permissions = [
            'users_list',
            'users_add',
            'users_view',
            'users_view_invisibles',
            'users_edit',
            'users_edit_permissions',
            'users_edit_password',
            'users_edit_privacy',
            'users_delete',
            'users_settings',
            'users_edit_credit',
            'users_login',
            'users_export',

            'logs_search',
            'logs_search_system_logs',
            'logs_view',
            'logs_delete',

            'profile_view',
            'profile_edit',
            'profile_edit_privacy',
            'profile_settings',

            'settings_usertypes_list',
            'settings_usertypes_add',
            'settings_usertypes_edit',
            'settings_usertypes_delete',

            'resetpwd_newpwd',

            'settings_general-settings',
        ];
        foreach ($permissions as $permission) {
            Permissions::add('userpanel_'.$permission);
        }
    }
}
