<?php

namespace packages\userpanel\listeners;

use packages\userpanel\Authentication\SessionHandler;
use packages\userpanel\Events\AuthenticationHandlersEvent as Event;

class AuthenticationHandlers
{
    /**
     * Add built-in authentication method to the list.
     *
     * @param AuthenticationHandlersEvent $e
     *
     * @return void
     */
    public function add(Event $e)
    {
        $e->addHandler(SessionHandler::class);
    }
}
