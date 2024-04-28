<?php

namespace packages\userpanel\Views\Users;

use packages\base\Views\Traits\Form as FormTrait;
use packages\userpanel\Authorization;
use packages\userpanel\Views\ListView as list_view;

class ListView extends list_view
{
    use FormTrait;
    protected $canView;
    protected $canEdit;
    protected $canDelete;
    protected $canExport;
    protected static $navigation;

    public function __construct()
    {
        $this->canView = Authorization::is_accessed('users_view');
        $this->canEdit = Authorization::is_accessed('users_edit');
        $this->canDelete = Authorization::is_accessed('users_delete');
        $this->canExport = Authorization::is_accessed('users_export');
    }

    public static function onSourceLoad()
    {
        self::$navigation = Authorization::is_accessed('users_list');
    }

    public function setUserTypes($types)
    {
        $this->setData($types, 'usertypes');
    }

    public function getUserTypes()
    {
        return $this->getData('usertypes');
    }
}
