<?php

namespace packages\userpanel\Views\Users;

use packages\userpanel\Authorization;
use packages\userpanel\View as ParentView;

class View extends ParentView
{
    use SettingsTrait;

    /**
     * @var bool
     */
    protected $canEdit;

    /**
     * @var bool
     */
    protected $canLogin;

    /**
     * @var bool
     */
    protected $canViewInvisibles;

    public function __construct()
    {
        $this->canEdit = Authorization::is_accessed('users_edit');
        $this->canViewInvisibles = Authorization::is_accessed('users_view_invisibles');
        $this->canLogin = Authorization::is_accessed('users_login');
    }

    public function setUserData($data): void
    {
        $this->setData($data, 'user');
    }

    public function getUserData(string $key)
    {
        return $this->data['user']->{$key};
    }

    public function canViewLog(): bool
    {
        return Authorization::is_accessed('logs_view');
    }
}
