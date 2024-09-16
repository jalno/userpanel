<?php

namespace packages\userpanel\Views\Settings\UserTypes;

use packages\base\Views\Traits\Form as FormTrait;
use packages\userpanel\Authorization;
use packages\userpanel\Views\ListView as list_view;

class ListView extends list_view
{
    use FormTrait;
    protected $canAdd;
    protected $canEdit;
    protected $canDelete;
    protected $usertypes;

    public function __construct()
    {
        $this->canAdd = Authorization::is_accessed('settings_usertypes_add');
        $this->canEdit = Authorization::is_accessed('settings_usertypes_edit');
        $this->canDelete = Authorization::is_accessed('settings_usertypes_delete');
    }

    public function getUserTypes()
    {
        return $this->dataList;
    }
}
