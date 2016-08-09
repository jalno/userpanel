<?php
namespace packages\userpanel;
use packages\base\db\dbObject;
class log_param extends dbObject{
	protected $dbTable = "userpanel_logs_params";
	protected $primaryKey = "";
	protected $dbFields = array(
        'log' => array('type' => 'int', 'required' => true),
        'name' => array('type' => 'text', 'required' => true),
        'value' => array('type' => 'text', 'required' => true)
    );
	protected $jsonFields = array('value');
}
