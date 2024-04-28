<?php

namespace themes\clipone\Views\Logs;

use packages\userpanel;
use packages\userpanel\Views;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\ViewTrait;

class View extends Views\Logs\View
{
    use ViewTrait;
    protected $log;
    protected $handler;

    public function __beforeLoad(): void
    {
        $this->log = $this->getLog();
        $this->handler = $this->log->getHandler();
        $this->setTitle(t('logs.view.title', ['log' => $this->log->id]));
        $this->addBodyClass('logs');
        $this->addBodyClass('logs_view');
        $this->setNavigation();
    }

    private function setNavigation(): void
    {
        if (null != Navigation::getByName('logs')) {
            Breadcrumb::addItem(Navigation::getByName('logs'));
            $item = new Navigation\MenuItem('log');
            $item->setTitle($this->getTitle());
            $item->setIcon('fa fa-exclamation-circle');
            Breadcrumb::addItem($item);
            Navigation::active('logs');
        } else {
            Navigation::active('dashboard');
            $item = new Navigation\MenuItem('profile');
            $item->setTitle(t('profile.view'));
            $item->setURL(userpanel\url('profile/view'));
            $item->setIcon('clip-user');
            Breadcrumb::addItem($item);
            $item = new Navigation\MenuItem('profile-view');
            $item->setTitle($this->getTitle());
            $item->setIcon('fa fa-exclamation-circle');
            Breadcrumb::addItem($item);
        }
    }
}
