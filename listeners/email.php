<?php
namespace packages\userpanel\listeners;
use \packages\email\template;
use \packages\email\events\templates;
class email{
	public function templates(templates $event){
		$event->addTemplate($this->tokenTemplate());
	}
	private function tokenTemplate(){
		$template = new template();
		$template->name = 'userpanel_resetpwd_token';
		$template->addVariable('\\packages\\userpanel\\resetpwd\\token');
		return $template;
	}
}
