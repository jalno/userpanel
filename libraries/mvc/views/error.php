<?php
namespace packages\userpanel\views;
use \packages\userpanel\view;
class ErrorView extends view{
	protected $errorcode;
	protected $errortext;
	function __construct(){
		parent::__construct("errors.php");
	}
	public function setErrorCode($code){
		$this->errorcode = $code;
	}
	public function setErrorText($text){
		$this->errortext = $text;
	}
}
