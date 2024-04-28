<?php

namespace themes\clipone\Views\Logs;

use packages\userpanel\Views;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\ViewTrait;

class Delete extends Views\Logs\Delete
{
    use ViewTrait;
    protected $log;

    public function __beforeLoad()
    {
        $this->log = $this->getLog();
        $this->setTitle(t('logs.delete.title', ['log' => $this->log->id]));
        $this->addBodyClass('logs');
        $this->addBodyClass('logs-delete');
        $this->setNavigation();
    }

    private function setNavigation()
    {
        Breadcrumb::addItem(Navigation::getByName('logs'));
        $item = new Navigation\MenuItem('log');
        $item->setTitle($this->getTitle());
        $item->setIcon('fa fa-exclamation-circle');
        Breadcrumb::addItem($item);
        Navigation::active('logs');
    }
}
