<?php

namespace themes\clipone\listeners;

use packages\base\View;
use packages\userpanel;
use packages\userpanel\{Authorization};
use themes\clipone\Events;
use themes\clipone\Tab;
use themes\clipone\Views;

class UserProfileTabs
{
    public function handle(Events\InitTabsEvent $event)
    {
        $view = $event->getView();
        if ($view instanceof Views\Profile\Index) {
            $this->addToProfile($view);
        } elseif ($view instanceof Views\Users\View) {
            $this->addToUserAdmin($view);
        }
    }

    private function addToProfile(View $view)
    {
        if (Authorization::is_accessed('profile_view')) {
            $tab = new Tab('view', Views\Profile\View::class);
            $tab->setTitle(t('profile.view'));
            $tab->setLink(userpanel\url('profile/view'));
            $view->addTab($tab);
        }

        if (Authorization::is_accessed('profile_edit')) {
            $tab = new Tab('edit', Views\Profile\Edit::class);
            $tab->setTitle(t('profile.edit'));
            $tab->setLink(userpanel\url('profile/edit'));
            $view->addTab($tab);
        }

        if (Authorization::is_accessed('profile_settings')) {
            $tab = new Tab('settings', Views\Profile\Settings::class);
            $tab->setTitle(t('profile.settings'));
            $tab->setLink(userpanel\url('profile/settings'));
            $view->addTab($tab);
        }
    }

    private function addToUserAdmin(View $view)
    {
        $user = $view->getData('user')->id;

        if (Authorization::is_accessed('users_view')) {
            $tab = new Tab('view', Views\Users\OverView::class);
            $tab->setTitle(t('user.profile.overview'));
            $tab->setLink(userpanel\url('users/view/'.$user));
            $view->addTab($tab);
        }
        if (Authorization::is_accessed('users_edit')) {
            $tab = new Tab('edit', Views\Users\Edit::class);
            $tab->setTitle(t('profile.edit'));
            $tab->setLink(userpanel\url('users/edit/'.$user));
            $view->addTab($tab);
        }
        if (Authorization::is_accessed('users_settings')) {
            $tab = new Tab('settings', Views\Users\Settings::class);
            $tab->setTitle(t('users.settings'));
            $tab->setLink(userpanel\url('users/settings/'.$user));
            $view->addTab($tab);
        }
    }
}
