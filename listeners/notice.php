<?php
namespace packages\userpanel\listeners;
use \packages\userpanel\views;
use \packages\notice\events\views as event;
use \packages\notice\events\views\view;

class notice{
	public function views(event $event){
		$event->addView(new view(views\dashboard::class));
		$event->addView(new view(views\forbidden::class));
		$event->addView(new view(views\notfound::class));
		$event->addView(new view(views\profile\view::class));
		$event->addView(new view(views\profile\edit::class));
		$event->addView(new view(views\profile\settings::class));
		$event->addView(new view(views\users\listview::class));
		$event->addView(new view(views\users\add::class));
		$event->addView(new view(views\users\edit::class));
		$event->addView(new view(views\users\delete::class));
		$event->addView(new view(views\users\view::class));
		$event->addView(new view(views\users\settings::class));
		$event->addView(new view(views\settings\usertypes\listview::class));
		$event->addView(new view(views\settings\usertypes\add::class));
		$event->addView(new view(views\settings\usertypes\edit::class));
		$event->addView(new view(views\settings\usertypes\delete::class));
	}
}
