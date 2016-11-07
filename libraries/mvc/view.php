<?php
namespace packages\userpanel;
trait viewTrait{
	protected $shortdescription;
	public function setShortDescription($description){
		$this->shortdescription = $description;
	}
	public function getShortDescription(){
		return $this->shortdescription;
	}
	public function output(){
		if($user = authentication::getUser()){
			$user->lastonline = time();
			$user->save();
		}
		parent::output();
	}
}
class view extends \packages\base\view{
	use viewTrait;
}
