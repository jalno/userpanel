<?php
namespace packages\userpanel\listeners\settings;
use \packages\userpanel\usertype\permissions;
class usertype{
	public function permissions_list(){
		$permissions = array(
			'users_list',
			'users_add',
			'users_view',
			'users_view_invisibles',
			'users_edit',
			'users_edit_privacy',
			'users_delete',
			'users_settings',
			"users_edit_credit",

			'logs_search',
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
		);
		foreach($permissions as $permission){
			permissions::add('userpanel_'.$permission);
		}
	}
}
