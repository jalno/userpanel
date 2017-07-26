<?php
namespace packages\userpanel\listeners;
use \packages\sms\template;
use \packages\sms\events\templates;
class sms{
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
