<?php

namespace packages\userpanel\User;

use packages\base\DB\DBObject;

class SocialNetwork extends DBObject
{
    public const facebook = 1;
    public const twitter = 2;
    public const gplus = 3;
    public const instagram = 4;
    public const telegram = 5;
    public const skype = 6;

    protected $dbTable = 'userpanel_users_socialnetworks';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'user' => ['type' => 'int', 'required' => true],
        'network' => ['type' => 'int', 'required' => true],
        'username' => ['type' => 'text', 'required' => true],
    ];

    public function getURL()
    {
        switch ($this->network) {
            case self::telegram:
                return "https://telegram.me/{$this->username}";
                break;
            case self::instagram:
                return "https://instagram.com/{$this->username}";
                break;
            case self::skype:
                return "skype:{$this->username}";
                break;
            case self::twitter:
                return "https://twitter.com/{$this->username}";
                break;
            case self::facebook:
                return "https://facebook.com/{$this->username}";
                break;
            case self::gplus:
                return "https://plus.google.com/{$this->username}";
                break;
        }

        return null;
    }
}
