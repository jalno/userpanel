<?php
namespace packages\userpanel;
use \packages\base\events;
use \packages\userpanel\events\search as searchEvent;
use \packages\userpanel\search\result;
class search{
	static protected $results = array();
	static public $totalCount = 0;
	static public $ipp = 25;
	static public function addResult(result $result){
		self::$results[] = $result;
	}
	static public function find($word){
		events::trigger(new searchEvent($word));
		self::$totalCount = count(self::$results);
		return self::$results;
	}
	static public function paginate($word, $page){
		events::trigger(new searchEvent($word));
		self::$totalCount = count(self::$results);
		return array_slice(self::$results, self::$ipp*($page-1), self::$ipp);
	}
}
