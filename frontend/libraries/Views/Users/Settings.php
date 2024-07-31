<?php

namespace themes\clipone\Views\Users;

use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Views\Users\Settings as UsersSettingsView;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\TabTrait;
use themes\clipone\ViewTrait;

class Settings extends UsersSettingsView
{
    use ViewTrait;
    use FormTrait;
    use TabTrait;
    protected $user;

    public function __beforeLoad()
    {
        $this->user = $this->getUser();
        $this->setTitle(t('users.settings'));

        $this->addBodyClass('users');
        $this->addBodyClass('users-settings');
        $this->setNavigation();
    }

    private function setNavigation()
    {
        $item = new MenuItem('users');
        $item->setTitle(t('users'));
        $item->setURL(userpanel\url('users'));
        $item->setIcon('clip-users');
        Breadcrumb::addItem($item);

        $item = new MenuItem('user');
        $item->setTitle($this->getData('user')->getFullName());
        $item->setURL(userpanel\url('users/view/'.$this->getDataForm('id')));
        $item->setIcon('clip-user');
        Breadcrumb::addItem($item);

        $item = new MenuItem('settings');
        $item->setTitle(t('users.settings'));
        $item->setURL(userpanel\url('users/settings/'.$this->getDataForm('id')));
        $item->setIcon('fa fa-cog fa-spin');
        Breadcrumb::addItem($item);

        Navigation::active('users/list');
    }
}
