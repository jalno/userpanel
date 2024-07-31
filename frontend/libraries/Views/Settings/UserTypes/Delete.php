<?php

namespace themes\clipone\Views\Settings\UserTypes;

use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Views\Settings\UserTypes\Delete as UserTypesDelete;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class Delete extends UserTypesDelete
{
    use ViewTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $usertype = $this->getUserType();
        $this->setTitle(t('usertype.delete'));
        $this->setNavigation();
    }

    private function setNavigation()
    {
        $item = new MenuItem('settings');
        $item->setTitle(t('settings'));
        $item->setURL(userpanel\url('settings'));
        $item->setIcon('clip-settings');
        Breadcrumb::addItem($item);

        $item = new MenuItem('usertypes');
        $item->setTitle(t('usertypes'));
        $item->setURL(userpanel\url('settings/usertypes'));
        $item->setIcon('fa fa-group');
        Breadcrumb::addItem($item);

        $usertype = $this->getUserType();
        $item = new MenuItem('delete');
        $item->setTitle(t('usertype.delete.byTitle', ['title' => $usertype->title]));
        $item->setURL(userpanel\url('settings/usertypes/delete/'.$usertype->id));
        $item->setIcon('fa fa-trash');
        Breadcrumb::addItem($item);

        Navigation::active('settings/usertypes');
    }
}
