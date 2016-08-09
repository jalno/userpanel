<?php
namespace packages\userpanel;
use packages\base\db\dbObject;
class user_socialnetwork extends dbObject{
	const facebook = 1;
	const twitter = 2;
	const gplus = 3;
	const instagram = 4;
	const telegram = 5;
	
	protected $dbTable = "userpanel_users_socialnetworks";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'user' => array('type' => 'int', 'required' => true),
		'network' => array('type' => 'int', 'required' => true),
        'url' => array('type' => 'text', 'required' => true)
    );
}
