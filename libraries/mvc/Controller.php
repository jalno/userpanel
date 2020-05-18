<?php
namespace packages\userpanel;

use packages\base\{DB, Http, Controller as BaseController};

class Controller extends BaseController {

	/** @var int */
	protected $page = 1;

	/** @var int */
	protected $total_pages = 1;

	/** @var int */
	protected $items_per_page = 25;

	public function __construct() {
		parent::__construct();
		if (isset($this->authentication) and $this->authentication) {
			$this->checkAuth();
		}
		$this->page = Http::getURIData('page');
		$this->items_per_page = Http::getURIData('ipp');
		if($this->page < 1)$this->page = 1;
		if($this->items_per_page < 1)$this->items_per_page = 25;
		DB::pageLimit($this->items_per_page);
	}

	/**
	 * Check and send response (in the case) for non-auth users.
	 * 
	 * @throws AuthenticationException if user is not logged-in
	 * @return bool always true for backward compatibilty
	 */
	protected function checkAuth(): bool {
		if (!Authentication::check()) {
			throw new AuthenticationException();
		}
		return true;
	}
}
