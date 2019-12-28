<?php
namespace packages\userpanel\listeners;

use packages\notifications\Events;
use packages\userpanel\events as UserpanelEvents;

class Notifications {

	public function events(Events $events){
		$events->add(UserpanelEvents\ResetPWD::class);
	}

}