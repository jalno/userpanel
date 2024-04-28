<?php

namespace packages\userpanel\listeners;

use packages\notice\Events\Views as Event;
use packages\notice\Events\Views\View;
use packages\userpanel\Views;

class Notice
{
    public function views(Event $event)
    {
        $event->addView(new View(Views\Dashboard::class));
        $event->addView(new View(Views\Forbidden::class));
        $event->addView(new View(Views\NotFound::class));
        $event->addView(new View(Views\Profile\View::class));
        $event->addView(new View(Views\Profile\Edit::class));
        $event->addView(new View(Views\Profile\Settings::class));
        $event->addView(new View(Views\Users\ListView::class));
        $event->addView(new View(Views\Users\Add::class));
        $event->addView(new View(Views\Users\Edit::class));
        $event->addView(new View(Views\Users\Delete::class));
        $event->addView(new View(Views\Users\View::class));
        $event->addView(new View(Views\Users\Settings::class));
        $event->addView(new View(Views\Settings\UserTypes\ListView::class));
        $event->addView(new View(Views\Settings\UserTypes\Add::class));
        $event->addView(new View(Views\Settings\UserTypes\Edit::class));
        $event->addView(new View(Views\Settings\UserTypes\Delete::class));
    }
}
