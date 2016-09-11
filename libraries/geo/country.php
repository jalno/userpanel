<?php
namespace packages\userpanel;
use packages\base\db\dbObject;
class country extends dbObject{
	protected $dbTable = "userpanel_countries";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'code' => array('type' => 'text', 'required' => true, 'unique' => 'true'),
        'name' => array('type' => 'text', 'required' => true),
    );
}
