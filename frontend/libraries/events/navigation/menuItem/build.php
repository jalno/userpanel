<?php
namespace themes\clipone\events\navigation\menuItem;
use \packages\base\event;
use \themes\clipone\navigation\menuItem;
class build extends event{
	protected $item;
	public function __construct(menuItem $item){
		$this->item = $item;
	}
	public function getItem(){
		return $this->item;
	}
}
