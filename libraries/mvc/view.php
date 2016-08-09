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
}
class view extends \packages\base\view{
	use viewTrait;
}
