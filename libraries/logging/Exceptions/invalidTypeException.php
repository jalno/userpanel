<?php
namespace packages\userpanel\logging\Exception;
use packages\userpanel\logging\Exception;
class invalidTypeException extends Exception{
	private $type;
	public function __constructor(string $type, string $message = ''){
		$this->type = $type;
		parent::__constructor($message);
	}
	public function getType():string{
		return $this->type;
	}
};