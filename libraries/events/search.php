<?php
namespace packages\userpanel\events;
use \packages\base\event;
class search extends event{
	public $word;
	function __construct($word){
		$this->word = $word;
	}
}
