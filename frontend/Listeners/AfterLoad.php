<?php

namespace themes\clipone\Listeners;

use packages\base\Event;
use packages\base\Options;
use packages\Userpanel;

class AfterLoad
{
    public function setFavicon(Event $event)
    {
        $view = $event->getView();
        if ($view instanceof Userpanel\View and !$view->getFavicon()) {
            $faviconUrl = Options::get('packages.userpanel.favicon');
            if ($faviconUrl) {
                $view->setFavicon($faviconUrl);
            }
        }
    }
}
