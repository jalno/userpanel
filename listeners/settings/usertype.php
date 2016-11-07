<?php
namespace packages\userpanel\listeners\settings;
use \packages\userpanel\usertype\permissions;
class usertype{
	public function permissions_list(){
		$permissions = array(
			'users_list',
			'users_add',
			'users_view',
			'users_edit',
			'users_delete',

			'profile_view',
			'profile_edit',

			'settings_usertypes_list',
			'settings_usertypes_add',
			'settings_usertypes_edit',
			'settings_usertypes_delete',

			'settings_bankaccounts_list',
			'settings_bankaccounts_add',
			'settings_bankaccounts_delete',
			'settings_bankaccounts_edit'

		);
		foreach($permissions as $permission){
			permissions::add('userpanel_'.$permission);
		}
	}
}
