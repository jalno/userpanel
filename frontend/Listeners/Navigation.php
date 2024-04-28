<?php

namespace themes\clipone\Listeners;

use themes\clipone\Navigation as NavigationController;

class Navigation
{
    public function removeSettings()
    {
        if ($settings = NavigationController::getByName('settings')) {
            if ($settings->isEmpty()) {
                NavigationController::removeItem($settings);
            }
        }
    }
}
