<?php

namespace themes\clipone\Listeners;

use packages\userpanel\Authorization;
use themes\clipone\Navigation as NavigationController;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\Dashboard;

use function packages\userpanel\url;

class Navigation
{

    public function initial(): void
    {
        $item = new MenuItem('dashboard');
        $item->setTitle(t('dashboard'));
        $item->setURL(url());
        $item->setIcon('clip-home-3');
        $item->setPriority(0);
        NavigationController::addItem($item);

        if (Authorization::is_accessed('users_list')) {
            $item = new MenuItem('users');
            $item->setTitle(t('users'));
            $item->setURL(url('users'));
            $item->setIcon('clip-users');
            NavigationController::addItem($item);
        }

        if (Authorization::is_accessed('logs_search')) {
            $item = new MenuItem('logs');
            $item->setTitle(t('users.logs'));
            $item->setURL(url('logs/search'));
            $item->setIcon('fa fa-user-secret');
            NavigationController::addItem($item);
        }
    
        $settings = Dashboard::getSettingsMenu();
        NavigationController::addItem($settings);
    
        if (Authorization::is_accessed('settings_general-settings')) {
            $item = new MenuItem('userpanel_general-settings');
            $item->setTitle(t('userpanel.general-settings'));
            $item->setURL(url('settings'));
            $item->setIcon('fa fa-cogs');
            $settings->addItem($item);
        }

        if (Authorization::is_accessed('settings_usertypes_list')) {
            $usertype = new MenuItem('usertypes');
            $usertype->setTitle(t('usertypes'));
            $usertype->setURL(url('settings/usertypes'));
            $usertype->setIcon('fa fa-address-card-o');
            $settings->addItem($usertype);
        }
    }

    public function removeSettings(): void
    {
        if ($settings = NavigationController::getByName('settings')) {
            if ($settings->isEmpty()) {
                NavigationController::removeItem($settings);
            }
        }
    }
}
