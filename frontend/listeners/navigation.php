<?php
namespace themes\clipone\listeners;
use \themes\clipone\navigation as navigationController;
class navigation{
	public function removeSettings(){
		if($settings = navigationController::getByName("settings")){
			if($settings->isEmpty()){
				navigationController::removeItem($settings);
			}
		}
	}
}
