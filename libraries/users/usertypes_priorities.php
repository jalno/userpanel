<?php
namespace packages\userpanel;
use packages\base\db\dbObject;
class usertype_priority extends dbObject{
	protected $dbTable = "userpanel_usertypes_priorities";
	protected $primaryKey = "parent";
	public $returnType = 'Array';
	protected $dbFields = array(
        'parent' => array('type' => 'int', 'required' => true),
        'child' => array('type' => 'int', 'required' => true)
    );
	protected $relations = array(
        'parentdata' => array("hasOne", "packages\\userpanel\\usertype", "parent"),
		'childdata' => array('hasOne', 'packages\\userpanel\\usertype', 'child')
    );
}
