<?php
namespace packages\userpanel\controllers;

use packages\base\{Http, Response};
use packages\userpanel;
use packages\userpanel\{Controller, View, Authentication, Search, views, events};

class Dashboard extends Controller {

	public function index(): Response {
		$this->checkAuth();
		$view = View::byName(views\Dashboard::class);
		$this->response->setView($view);
		$this->response->setStatus(true);
		return $this->response;
	}

	public function forbidden(): Response {

		// Loading user data but it's not necessary to user be logged in/
		Authentication::check();

		$this->response->setStatus(false);
		$this->response->setHttpCode(403);
		$view = View::byName(views\Forbidden::class);
		$this->response->setView($view);
		if ($this->response->is_api()) {
			$this->response->setData("forbidden", "error");
		}
		return $this->response;
	}

	public function notfound(): Response {

		// Loading user data but it's not necessary to user be logged in/
		Authentication::check();

		$this->response->setStatus(false);
		$this->response->setHttpCode(404);
		$view = View::byName(views\Notfound::class);
		$this->response->setView($view);
		return $this->response;
	}

	public function online(): Response {
		if (Authentication::check()) {
			Authentication::getUser()->online();
		}
		$event = new events\Online();
		$event->trigger();
		if ($response = $event->getResponse()) {
			$this->response->setData($response);
		}
		$this->response->setStatus(true);
		return $this->response;
	}

	public function search(): Response {
		$this->checkAuth();
		$view = View::byName(views\Search::class);
		$this->response->setView($view);
		$this->response->setStatus(true);
		$inputs = $this->checkinputs(array(
			'word' => array(
				'type' => 'string',
				'optional' => true,
			)
		));
		if (isset($inputs['word'])) {
			Search::$ipp = $this->items_per_page;
			$view->setResults(Search::paginate($inputs['word'], $this->page));
			$view->setPaginate($this->page, Search::$totalCount, $this->items_per_page);
		}
		return $this->response;
	}

	public function authError(): Response {
		$this->response->setStatus(false);
		if (userpanel\url() == Http::$request['uri']) {
			$this->response->go(userpanel\url('login'));
		} else {
			$indexurl = parse_url(userpanel\url('', [], true));
			if (!isset($indexurl['port'])) {
				switch($indexurl['scheme']){
					case('http'):
						$indexurl['port'] = 80;
						break;
					case('https'):
						$indexurl['port'] = 443;
						break;
				}
			}
			if ($indexurl['scheme'] == Http::$request['scheme'] and $indexurl['host'] == Http::$request['hostname'] and $indexurl['port'] == Http::$server['port']) {
				$this->response->go(userpanel\url('login', ['backTo' => Http::$request['uri'] . (Http::$request['get'] ? "?" . http_build_query(Http::$request['get']) : "")]));
			} else {
				$this->response->go(userpanel\url('login', ['backTo' => Http::getURL()]));
			}
		}
		if ($this->response->is_ajax() or $this->response->is_api()) {
			$this->response->setHttpCode(401);
		}
		return $this->response;
	}
}
