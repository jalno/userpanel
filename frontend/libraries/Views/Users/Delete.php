<?php

namespace themes\clipone\Views\Users;

use packages\userpanel;
use packages\userpanel\Views\Users\Delete as ParentView;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class Delete extends ParentView
{
    use FormTrait;
    use ViewTrait;

    protected $usertypes = [];

    public function __beforeLoad(): void
    {
        $this->setTitle(t('user.delete.warning.title'));

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
        $item->setTitle($this->getDataForm('name'));
        $item->setURL(userpanel\url('users/view/'.$this->getDataForm('id')));
        $item->setIcon('clip-user');
        Breadcrumb::addItem($item);

        $item = new MenuItem('edit');
        $item->setTitle(t('user.delete'));
        $item->setURL(userpanel\url('users/edit/'.$this->getDataForm('id')));
        $item->setIcon('clip-edit');
        Breadcrumb::addItem($item);

        Navigation::active('users/list');
    }
}
