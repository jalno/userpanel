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
use \packages\base\image;
use \packages\base\IO;
use \packages\base\packages;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\usertype;
use \packages\userpanel\authorization;
use \packages\userpanel\authentication;
use \packages\userpanel\controller;
use \packages\userpanel\view;
use \packages\userpanel\country;
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
		authorization::haveOrFail('users_add');
		$view = view::byName("\\packages\\userpanel\\views\\users\\add");
		$types = authorization::childrenTypes();
		$view->setCountries(country::get());
		$view->setTypes(usertype::where("id", $types, 'in')->get());
		if(http::is_post()){
			$inputs = array(
				'name' => array(
					'type' => 'string'
				),
				'lastname' => array(
					'type' => 'string',
					'optional' => true,
					'empty' => true
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
				'zip' => array(
					'optional' => true,
					'type' => 'number',
					'empty' => true
				),
				'city' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'country' => array(
					'optional' => true,
					'type' => 'number',
					'empty' => true
				),
				'address' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'phone' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'credit' => array(
					'optional' => true,
					'type' => 'number',
					'empty' => true
				),
				'status' => array(
					'type' => 'number',
					'values' => array(user::active, user::deactive,user::suspend)
				)
			);
			$this->response->setStatus(false);
			try{
				$formdata = $this->checkinputs($inputs);
				if(!usertype::byId($formdata['type'])){
					throw new inputValidation("type");
				}
				if(isset($formdata['country'])){
					if(!country::byId($formdata['country'])){
						throw new inputValidation("country");
					}
				}
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
			$view->setDataForm(105,'country');
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function view($data){
		authorization::haveOrFail('users_view');
		$user = user::with('type')->with('socialnetworks')->byId($data['user']);
		if(!$user){
			throw new NotFound;
		}
		$view = view::byName("\\packages\\userpanel\\views\\users\\view");
		$view->setUserData($user);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('users_edit');
		$types = authorization::childrenTypes();
		$user = user::where("id", $data['user'])->where("type", $types, 'in')->getOne();
		if(!$user->id){
			throw new NotFound();
		}
		$view = view::byName("\\packages\\userpanel\\views\\users\\edit");
		$view->setTypes(usertype::where("id", $types, 'in')->get());
		$view->setCountries(country::get());
		if(http::is_post()){
			$inputs = array(
				'name' => array(
					'optional' => true,
					'type' => 'string'
				),
				'lastname' => array(
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
				'zip' => array(
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
				'phone' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'credit' => array(
					'optional' => true,
					'type' => 'number',
					'empty' => true
				),
				'status' => array(
					'optional' => true,
					'type' => 'number',
					'values' => array(user::active, user::deactive,user::suspend)
				),
				'avatar' => array(
					'optional' => true,
					'type' => 'file'
				)
			);
			$this->response->setStatus(false);
			try{
				$formdata = $this->checkinputs($inputs);
				if(isset($formdata['type']) and !usertype::byId($formdata['type'])){
					throw new inputValidation("type");
				}
				if(isset($formdata['country'])){
					if(!country::byId($formdata['country'])){
						throw new inputValidation("country");
					}
				}

				if(isset($formdata['avatar'])){
					if($formdata['avatar']['error'] == 0){
						$type = image::getType($formdata['avatar']['tmp_name']);
						if(in_array($type, array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
							$directory = packages::package('userpanel')->getFilePath('storage/public/avatars');
							if(!is_dir($directory)){
								IO\mkdir($directory, true);
							}
							
							$image = new image($formdata['avatar']['tmp_name']);
							$tmpfile = $directory."/rand".((time() + rand(0, 10000)) * rand(0, 100)  / 100);
							$image->resize(200,200);
							$image->save($tmpfile, $type);
							$name = md5_file($tmpfile);
							if($type == IMAGETYPE_JPEG){
								$type_name = '.jpg';
							}elseif($type == IMAGETYPE_GIF){
								$type_name = '.gif';
							}elseif($type == IMAGETYPE_PNG){
								$type_name = '.png';
							}

							if(rename($tmpfile, $directory.'/'.$name.$type_name)){
								$formdata['avatar'] = "storage/public/avatars/".$name.$type_name;
							}else{
								throw new inputValidation("avatar");
							}
						}else{
							throw new inputValidation("avatar");
						}
					}elseif(isset($formdata['avatar']['error']) and $formdata['avatar']['error'] != 4){
						throw new inputValidation("avatar");
					}
				}

				if(isset($formdata['password']) and $formdata['password']){
					$user->password_hash($formdata['password']);
				}
				unset($formdata['password']);
				if(!is_string($formdata['avatar'])){
					unset($formdata['avatar']);
				}
				$user->save($formdata);
				unset($formdata['avatar']);
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
	public function delete($data){
		authorization::haveOrFail('users_delete');
		$types = authorization::childrenTypes();
		$user = user::where("id", $data['user'])->where("type", $types, 'in')->getOne();
		if(!$user){
			throw new NotFound;
		}
		$view = view::byName("\\packages\\userpanel\\views\\users\\delete");
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
}
