<?php

namespace themes\clipone\Views\Profile;

use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Views\Profile\Settings as ProfileSettingsView;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\TabTrait;
use themes\clipone\ViewTrait;

class Settings extends ProfileSettingsView
{
    use ViewTrait;
    use FormTrait;
    use TabTrait;
    protected $user;

    public function __beforeLoad()
    {
        $this->user = $this->getUser();
        $this->setTitle(Translator::trans('profile.settings'));

        $this->addBodyClass('profile');
        $this->addBodyClass('profile-settings');
        $this->setNavigation();
    }

    private function setNavigation()
    {
        $item = new MenuItem('profile');
        $item->setTitle(Translator::trans('profile.view'));
        $item->setURL(userpanel\url('profile/view'));
        $item->setIcon('fa fa-user');
        Breadcrumb::addItem($item);

        $settingsItem = new MenuItem('settings');
        $settingsItem->setTitle(Translator::trans('profile.settings'));
        $settingsItem->setURL(userpanel\url('profile/settings'));
        $settingsItem->setIcon('fa fa-cog fa-spin');
        Breadcrumb::addItem($settingsItem);

        Navigation::active('dashboard');
    }
}
