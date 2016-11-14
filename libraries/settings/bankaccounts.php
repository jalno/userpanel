<?php
namespace packages\userpanel\settings;
use packages\base\db\dbObject;
class bank_account extends dbObject{
	protected $dbTable = "userpanel_bankaccounts";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'bank' => array('type' => 'text', 'required' => true),
        'accnum' => array('type' => 'text', 'required' => true),
        'cartnum' => array('type' => 'text', 'required' => true),
        'master' => array('type' => 'text', 'required' => true)
    );
}
