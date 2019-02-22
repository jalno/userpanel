<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\base\inputValidation;
use \packages\userpanel\controller;
use \packages\userpanel\view;
use \packages\userpanel\authentication;
use \packages\userpanel\search;
use \packages\userpanel\views;
class dashboard extends controller{
	function index(){
		if(authentication::check()){
			if($view = view::byName("\\packages\\userpanel\\views\\dashboard")){
				$this->response->setView($view);
				return $this->response;
			}
		}else{
			parent::response(authentication::FailResponse());
		}
	}
	public function forbidden(){
		authentication::check();
		$this->response->setHttpCode(103);
		if($view = view::byName(views\forbidden::class)){
			$this->response->setView($view);
		}
		$this->response->setStatus(false);
		return $this->response;
	}
	public function notfound(){
		authentication::check();
		$this->response->setHttpCode(404);
		if($view = view::byName("\\packages\\userpanel\\views\\notfound")){
			$this->response->setView($view);
		}
		return $this->response;
	}

	public function online(){
		if(authentication::check()){
			authentication::getUser()->online();
		}
		$this->response->setStatus(true);
		return $this->response;
	}
	public function goToLogin(){
		return authentication::FailResponse();
	}
	public function search(){
		if(authentication::check()){
			$view = view::byName("\\packages\\userpanel\\views\\search");

			$this->response->setStatus(true);
			$inputsRules = array(
				'word' => array(
					'type' => 'string',
					'optional' => true,
					'empty' => true
				)
			);
			try{
				$inputs = $this->checkinputs($inputsRules);
				if($inputs['word']){
					search::$ipp = $this->items_per_page;
					$view->setResults(search::paginate($inputs['word'], $this->page));
					$view->setPaginate($this->page, search::$totalCount, $this->items_per_page);
				}
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
				$this->response->setStatus(false);
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
			$this->response->setView($view);
		}else{
			$this->response = authentication::FailResponse();
		}

		return $this->response;
	}
}
