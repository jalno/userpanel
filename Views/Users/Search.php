<?php

namespace packages\userpanel\Views\Users;

use packages\base\Views\Traits\Form;
use packages\userpanel\Authorization;
use packages\userpanel\Views\ListView;

class Search extends ListView
{
    use Form;

    public static function onSourceLoad()
    {
        self::$navigation = Authorization::is_accessed('users_list');
    }

    protected static $navigation;

    protected $canView;

    protected $canEdit;

    protected $canDelete;

    protected $canExport;

    public function __construct()
    {
        $this->canView = Authorization::is_accessed('users_view');
        $this->canEdit = Authorization::is_accessed('users_edit');
        $this->canDelete = Authorization::is_accessed('users_delete');
        $this->canExport = Authorization::is_accessed('users_export');
    }

    public function setUserTypes(?array $types): void
    {
        $this->setData($types, 'usertypes');
    }

    public function getUserTypes(): ?array
    {
        return $this->getData('usertypes');
    }

    public function setCountries(array $countries): void
    {
        $this->setData($countries, 'countries');
    }

    public function getCountries(): ?array
    {
        return $this->getData('countries');
    }
}
