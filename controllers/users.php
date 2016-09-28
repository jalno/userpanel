<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\base\http;
use \packages\base\db;
use \packages\base\inputValidation;
use \packages\base\db\duplicateRecord;
use \packages\base\db\InputDataType;
use \packages\base\db\parenthesis;
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
		authorization::haveOrFail('users_list');
		$view = view::byName("\\packages\\userpanel\\views\\users\\listview");
		$types = authorization::childrenTypes();

		$user = new user();
		if($types){
			$user->where("type", $types, 'in');
		}else{
			$user->where("id", authentication::getID());
		}
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true,
			),
			'name' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
			),
			'lastname' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
			),
			'email' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
			),
			'cellphone' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
			),
			'type' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'status' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'word' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'comparison' => array(
				'values' => array('equals', 'startswith', 'contains'),
				'default' => 'contains',
				'optional' => true
			)
		);
		try{
			$inputs = $this->checkinputs($inputsRules);
			if(isset($inputs['type']) and $inputs['type']){
				if(!in_array($inputs['type'], $types)){
					throw new inputValidation("type");
				}
			}
			if(isset($inputs['status']) and $inputs['status'] !== ''){
				if(!in_array($inputs['status'], array(user::active, user::deactive, user::suspend))){
					throw new inputValidation("status");
				}
			}
			foreach(array('id', 'name', 'lastname', 'type', 'email', 'cellphone', 'status') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('type', 'status'))){
						$comparison = 'equals';
					}
					$user->where($item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(array('name', 'lastname', 'email', 'cellphone') as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where($item,$inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				$user->where($parenthesis);
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
		}
		$view->setDataForm($this->inputsvalue($inputs));
		$user->pageLimit = $this->items_per_page;
		$users = $user->paginate($this->page);
		$this->total_pages = $user->totalPages;

		$view->setDataList($users);
		$view->setPaginate($this->page, $user->totalCount, $this->items_per_page);
		$view->setUserTypes(usertype::where("id", $types, 'in')->get());

		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
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
