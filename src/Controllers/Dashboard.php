<?php

namespace packages\userpanel\Controllers;

use packages\base\Http;
use packages\base\Response;
use packages\userpanel;
use packages\userpanel\Authentication;
use packages\userpanel\Controller;
use packages\userpanel\Events;
use packages\userpanel\Search;
use packages\userpanel\View;
use themes\clipone\Views;

class Dashboard extends Controller
{
    public function index(): Response
    {
        $this->checkAuth();
        $view = View::byName(Views\Dashboard::class);
        $this->response->setView($view);
        $this->response->setStatus(true);

        return $this->response;
    }

    public function forbidden(): Response
    {
        // Loading user data but it's not necessary to user be logged in/
        Authentication::check();

        $this->response->setStatus(false);
        $this->response->setHttpCode(403);
        $view = View::byName(Views\Forbidden::class);
        $this->response->setView($view);
        if ($this->response->is_api()) {
            $this->response->setData('forbidden', 'error');
        }

        return $this->response;
    }

    public function notfound(): Response
    {
        // Loading user data but it's not necessary to user be logged in/
        Authentication::check();

        $this->response->setStatus(false);
        $this->response->setHttpCode(404);
        $view = View::byName(Views\NotFound::class);
        $this->response->setView($view);

        return $this->response;
    }

    public function online(): Response
    {
        if (Authentication::check()) {
            Authentication::getUser()->online();
        }
        $event = new Events\Online();
        $event->trigger();
        if ($response = $event->getResponse()) {
            $this->response->setData($response);
        }
        $this->response->setStatus(true);

        return $this->response;
    }

    public function search(): Response
    {
        $this->checkAuth();
        $view = View::byName(Views\Search::class);
        $this->response->setView($view);
        $this->response->setStatus(true);
        $inputs = $this->checkinputs([
            'word' => [
                'type' => 'string',
                'optional' => true,
            ],
        ]);
        if (isset($inputs['word'])) {
            Search::$ipp = $this->items_per_page;
            $view->setResults(Search::paginate($inputs['word'], $this->page));
            $view->setPaginate($this->page, Search::$totalCount, $this->items_per_page);
        }

        return $this->response;
    }
}
