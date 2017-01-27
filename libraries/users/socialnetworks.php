<?php
namespace packages\userpanel\user;
use packages\base\db\dbObject;
class socialnetwork extends dbObject{
	const facebook = 1;
	const twitter = 2;
	const gplus = 3;
	const instagram = 4;
	const telegram = 5;
	const skype = 6;

	protected $dbTable = "userpanel_users_socialnetworks";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'user' => array('type' => 'int', 'required' => true),
		'network' => array('type' => 'int', 'required' => true),
        'username' => array('type' => 'text', 'required' => true)
    );
	public function getURL(){
		switch($this->network){
			case(self::telegram):
				return "https://telegram.me/{$this->username}";
				break;
			case(self::instagram):
				return "https://instagram.com/{$this->username}";
				break;
			case(self::skype):
				return "skype:{$this->username}";
				break;
			case(self::twitter):
				return "https://twitter.com/{$this->username}";
				break;
			case(self::facebook):
				return "https://facebook.com/{$this->username}";
				break;
			case(self::gplus):
				return "https://plus.google.com/{$this->username}";
				break;
		}
		return null;
	}
}
