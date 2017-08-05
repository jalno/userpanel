<?php
namespace packages\userpanel\events;
use \packages\base\event;
use \packages\userpanel\user;
use \packages\userpanel\events\settings\tuning;
class settings extends event{
	private $settings = [];
	private $user;
	public function addTuning(tuning $tuning){
		$this->settings[$tuning->getName()] = $tuning;
	}
	public function getTuningNames(){
		return array_keys($this->settings);
	}
	public function getByName($name){
		return (isset($this->settings[$name]) ? $this->settings[$name] : null);
	}
	public function get(){
		return $this->settings;
	}
	public function setUser(user $user){
		$this->user = $user;
	}
	public function getUser():user{
		return $this->user;
	}
}
