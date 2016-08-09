<?php
namespace packages\userpanel;
use \packages\base\http;
use \packages\base\db;
use \packages\base\response;
class controller extends \packages\base\controller{
	protected $response;
	protected $page = 1;
	protected $total_pages = 1;
	protected $items_per_page = 25;
	function __construct(){
		if(!isset($this->authentication) or !$this->authentication or authentication::check()){
			$this->page = http::getURIData('page');
			$this->items_per_page = http::getURIData('ipp');
			if($this->page < 1)$this->page = 1;
			if($this->items_per_page < 1)$this->items_per_page = 1;
			db::pageLimit($this->items_per_page);
			$this->response = new response();
		}else{
			parent::response(authentication::FailResponse());
		}
	}
}
?>
