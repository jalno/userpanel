<?php

namespace packages\userpanel;

use packages\base\DB\DBObject;
use packages\base\Options;

class Country extends DBObject
{
    public static function getDefaultCountry(): self
    {
        $defaultCountryID = Options::get('packages.userpanel.country.default.id');
        if (!$defaultCountryID) {
            return (new self())->where('code', 'IR')->getOne();
        }

        return (new self())->byID($defaultCountryID);
    }

    protected $dbTable = 'userpanel_countries';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'code' => ['type' => 'text', 'required' => true, 'unique' => 'true'],
        'name' => ['type' => 'text', 'required' => true],
        'dialing_code' => ['type' => 'text', 'required' => true],
    ];
}
