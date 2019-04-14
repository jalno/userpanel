<?php
namespace packages\userpanel;

use packages\base\{db, http, Response};

class controller extends \packages\base\controller{
	/** @var packages\bsae\Response */
	protected $response;
	
	/** @var int */
	protected $page = 1;

	/** @var int */
	protected $total_pages = 1;

	/** @var int */
	protected $items_per_page = 25;

	public function __construct(){
		if(isset($this->authentication) and $this->authentication and !$this->checkAuth()) {
			return;
		}
		$this->page = http::getURIData('page');
		$this->items_per_page = http::getURIData('ipp');
		if($this->page < 1)$this->page = 1;
		if($this->items_per_page < 1)$this->items_per_page = 25;
		db::pageLimit($this->items_per_page);
		$this->response = new Response();
	}

	/**
	 * Check and send response (in the case) for non-auth users.
	 * 
	 * @return bool false if the user was non-auth
	 */
	protected function checkAuth(): bool {
		if (!Authentication::check()) {
			parent::response(Authentication::FailResponse());
			return false;
		}
		return true;
	}
}
