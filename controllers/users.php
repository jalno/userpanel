<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\base\http;
use \packages\base\inputValidation;
use \packages\base\db\duplicateRecord;
use \packages\base\db\InputDataType;
use \packages\base\views\FormError;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\usertype;
use \packages\userpanel\authorization;
use \packages\userpanel\authentication;
use \packages\userpanel\controller;
use \packages\userpanel\view;
use \packages\userpanel\log;


class users extends controller{
	protected $authentication = true;
	public function index(){
		if(authorization::is_accessed('users_list')){
			$user = new user();
			$user->with('type');
			$user->pageLimit = $this->items_per_page;
			$users = $user->paginate($this->page);
			$this->total_pages = $user->totalPages;


			if($view = view::byName("\\packages\\userpanel\\views\\users\\listview")){
				$view->setDataList($users);
				$view->setPaginate($this->page, $this->total_pages, $this->items_per_page);
				$this->response->setStatus(true);
				$this->response->setView($view);
				return $this->response;
			}
		}else{
			return authorization::FailResponse();
		}
	}
	public function add($data){
		if(authorization::is_accessed('users_add')){
			if($view = view::byName("\\packages\\userpanel\\views\\users\\add")){
				if(http::is_post()){
					$inputs = array(
						'name' => array(
							'type' => 'string'
						),
						'email' => array(
							'type' => 'email',
						),
						'cellphone' => array(
							'type' => 'cellphone',
						),
						'password' => array(),
						'type' => array(
							'type' => 'number'
						),
						'zipcode' => array(
							'optional' => true,
							'type' => 'number'
						),
						'city' => array(
							'optional' => true,
							'type' => 'string'
						),
						'country' => array(
							'optional' => true,
							'type' => 'string'
						),
						'address' => array(
							'optional' => true,
							'type' => 'string'
						),
						'status' => array(
							'type' => 'number',
							'values' => array(0,1,2)
						)
					);
					$this->response->setStatus(false);
					try{
						$formdata = $this->checkinputs($inputs);
						$user = new user($formdata);
						$user->password_hash($formdata['password']);
						unset($formdata['password']);
						if($user->save()){
							$log = new log();
							$log->type = log::user_edit;
							$log->users = array_unique(array($user->id, authentication::getID()));
							$log->params = array(
								'user' => $user->id,
								'inputs' => $formdata
							);
							$log->save();
							$this->response->setStatus(true);
							$this->response->go(userpanel\url('users/edit/'.$user->id));
						}
					}catch(inputValidation $error){
						$view->setFormError(FormError::fromException($error));
					}catch(InputDataType $error){
						$view->setFormError(FormError::fromException($error));
					}catch(duplicateRecord $error){
						$view->setFormError(FormError::fromException($error));
					}
					$view->setDataForm($this->inputsvalue($inputs));
				}else{
					$this->response->setStatus(true);
				}
				$this->response->setView($view);
				return $this->response;
			}
		}else{
			return authorization::FailResponse();
		}
	}
	public function view($data){
		if(authorization::is_accessed('users_view')){
			$user = user::with('type')->with('socialnetworks')->byId($data['user']);
			if($view = view::byName("\\packages\\userpanel\\views\\users\\view")){
				$view->setData($user, 'user');
				$this->response->setStatus(true);
				$this->response->setView($view);
				return $this->response;
			}
		}else{
			return authorization::FailResponse();
		}
	}
	public function edit($data){
		if(authorization::is_accessed('users_edit')){
			$user = user::byId($data['user']);

			if($view = view::byName("\\packages\\userpanel\\views\\users\\edit")){
				if(http::is_post()){
					$inputs = array(
						'name' => array(
							'optional' => true,
							'type' => 'string'
						),
						'email' => array(
							'type' => 'email',
							'optional' => true,
						),
						'cellphone' => array(
							'type' => 'cellphone',
							'optional' => true,
						),
						'password' => array(
							'optional' => true,
							'empty' => true
						),
						'type' => array(
							'optional' => true,
							'type' => 'number'
						),
						'zipcode' => array(
							'optional' => true,
							'type' => 'number'
						),
						'city' => array(
							'optional' => true,
							'type' => 'string'
						),
						'country' => array(
							'optional' => true,
							'type' => 'string'
						),
						'address' => array(
							'optional' => true,
							'type' => 'string'
						),
						'status' => array(
							'optional' => true,
							'type' => 'number',
							'values' => array(0,1,2)
						)
					);
					$this->response->setStatus(false);
					try{
						$formdata = $this->checkinputs($inputs);
						if(isset($formdata['password']) and $formdata['password']){
							$user->password_hash($formdata['password']);
						}
						unset($formdata['password']);
						$user->save($formdata);
						$log = new log();
						$log->type = log::user_edit;
						$log->users = array_unique(array($user->id, authentication::getID()));
						$log->params = array(
							'user' => $user->id,
							'inputs' => $formdata
						);
						$log->save();
						$this->response->setStatus(true);
						$view->setDataForm($user->toArray());
					}catch(inputValidation $error){
						$view->setFormError(FormError::fromException($error));
						$view->setDataForm($this->inputsvalue($inputs));
					}catch(InputDataType $error){
						$view->setFormError(FormError::fromException($error));
						$view->setDataForm($this->inputsvalue($inputs));
					}catch(duplicateRecord $error){
						$view->setFormError(FormError::fromException($error));
						$view->setDataForm($this->inputsvalue($inputs));
					}

				}else{
					$this->response->setStatus(true);
				}
				$view->setDataForm($user->toArray());
				$this->response->setView($view);
				return $this->response;
			}
		}else{
			return authorization::FailResponse();
		}
	}
	public function delete($data){
		if(authorization::is_accessed('users_delete')){
			$user = user::byId($data['user']);

			if($view = view::byName("\\packages\\userpanel\\views\\users\\delete")){
				if(http::is_post()){
					$user->delete();
					$log = new log();
					$log->type = log::user_delete;
					$log->users = array_unique(array($user->id, authentication::getID()));
					$log->params = array(
						'user' => $user->id,
					);
					$log->save();
					$this->response->setStatus(true);
					$this->response->go(userpanel\url('users'));
				}else{
					$this->response->setStatus(true);
					$view->setDataForm($user->toArray());
					$this->response->setView($view);
				}
				return $this->response;
			}
		}else{
			return authorization::FailResponse();
		}
	}
}
