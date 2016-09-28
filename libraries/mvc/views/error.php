<?php
namespace packages\userpanel\views;
use \packages\userpanel\view;
class ErrorView extends view{
	protected $errorcode;
	protected $errortext;
	public function setErrorCode($code){
		$this->errorcode = $code;
	}
	public function setErrorText($text){
		$this->errortext = $text;
	}
}
