<?php

namespace themes\clipone\Views\Settings\UserTypes;

use packages\userpanel\Views\Settings\UserTypes\ListView as usertype_list;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class ListView extends usertype_list
{
    use ViewTrait;
    use ListTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('usertypes'));
        $this->setButtons();
        Navigation::active('settings/usertypes');
    }

    public function setButtons()
    {
        $this->setButton('edit', $this->canEdit, [
            'title' => t('usertype.edit'),
            'icon' => 'fa fa-edit',
            'classes' => ['btn', 'btn-xs', 'btn-warning'],
        ]);
        $this->setButton('delete', $this->canDelete, [
            'title' => t('usertype.delete'),
            'icon' => 'fa fa-times',
            'classes' => ['btn', 'btn-xs', 'btn-bricky'],
        ]);
    }
}
