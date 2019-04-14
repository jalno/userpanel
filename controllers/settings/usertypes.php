<?php
namespace packages\userpanel\controllers\settings;
use \packages\base;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\db\parenthesis;
use \packages\base\views\FormError;
use \packages\base\view\error;
use \packages\base\inputValidation;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\view;
use \packages\userpanel\usertype;
use \packages\userpanel\controller;
use \packages\userpanel\authorization;
use \packages\userpanel\authentication;
use \packages\userpanel\usertype\permissions;
use \packages\userpanel\usertype\permission;
use \packages\userpanel\usertype\priority;

/**
  * Handler for usertypes
  * @author Mahdi Abedi <abedi@jeyserver.com>
  * @copyright 2016 JeyServer
  */
class usertypes extends controller{
	/**
	* @var bool require authentication
	*/
	protected $authentication = true;

	/**
	* Search and listing for usertypes
	* @throws inputValidation for input validation
	* @throws inputValidation if id value is not in childrenTypes
	* @return \packages\base\response
	*/
	public function listTypes(){
		authorization::haveOrFail('settings_usertypes_list');
		$view = view::byName("\\packages\\userpanel\\views\\settings\\usertypes\\listview");
		$types = authorization::childrenTypes();

		$usertype = new usertype();

		//make searching in to children types and if user does not have any, can not access to anything
		if($types){
			$usertype->where("id", $types, 'in');
		}else{
			$usertype->where("id", null, 'is');
		}
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true,
			),
			'title' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
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

			//checking id for being on children types
			if(isset($inputs['id']) and $inputs['id']){
				if(!in_array($inputs['id'], $types)){
					throw new inputValidation("id");
				}
			}

			//notmal search
			foreach(array('id', 'title') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id'))){
						$comparison = 'equals';
					}
					$usertype->where($item, $inputs[$item], $comparison);
				}
			}

			//search based on keyword
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(array('title') as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where($item,$inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				$usertype->where($parenthesis);
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
		}

		//refill the search form
		$view->setDataForm($this->inputsvalue($inputsRules));

		//query with respect for pagination process
		$usertype->pageLimit = $this->items_per_page;
		$usertypes = $usertype->paginate($this->page);
		$this->total_pages = $usertype->totalPages;
		$view->setDataList($usertypes);
		$view->setPaginate($this->page, $usertype->totalCount, $this->items_per_page);

		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	/**
	* add a usertype and it's permissions and relations
	* @return \packages\base\response
	*/
	public function add(){
		authorization::haveOrFail('settings_usertypes_add');
		$view = view::byName("\\packages\\userpanel\\views\\settings\\usertypes\\add");
		$me = authentication::getUser();
		if ($me->isManager()) {
			$allPermissions = permissions::get();
		} else {
			$allPermissions = $me->getPermissions();
		}
		$childrenTypes = authorization::childrenTypes();

		//pass data to view
		$view->setPermissions($allPermissions);
		$view->setChildrenTypes($childrenTypes ? usertype::where("id", $childrenTypes, 'in')->get() : array());

		if(http::is_post()){
			$this->response->setStatus(false);
			$inputsRules = array(
				'title' => array(
					'type' => 'string',
					'optional' => true
				),
				'permissions' => array(),
				'priorities' => array(
					'optional' => true
				)
			);
			try{
				$inputs = $this->checkinputs($inputsRules);

				//Extra validation for permissions and comparing with list of total permissions
				if(is_array($inputs['permissions'])){
					foreach($inputs['permissions'] as $key => $permission){
						if(!in_array($permission, $allPermissions)){
							throw new inputValidation("permissions[{$key}]");
						}
					}
				}else{
					throw new inputValidation("permissions");
				}

				//Extra validation for priorities
				if(isset($inputs['priorities'])){
					if(is_array($inputs['priorities'])){
						foreach($inputs['priorities'] as $key => $priority){
							if(!in_array($priority,$childrenTypes)){
								throw new inputValidation("priorities[{$key}]");
							}
						}
					}else{
						throw new inputValidation("priorities");
					}
				}else{
					//setting an empty array makes all relations to delete
					$inputs['priorities'] = array();
				}
				//save new usertype
				$usertype = new usertype();
				$usertype->title = $inputs['title'];
				$usertype->save();

				//add as child for user's usertype
				$parentTypes = $me->parentTypes();
				$parentTypes[] = $me->type->id;
				foreach (array_unique($parentTypes) as $type) {
					$priority = new priority;
					$priority->parent = $type;
					$priority->child = $usertype->id;
					$priority->save();
				}

				// Processing of adding permissions
				foreach($inputs['permissions'] as $permissionName){
					$permission = new permission;
					$permission->type = $usertype->id;
					$permission->name = $permissionName;
					$permission->save();
				}

				// Processing of adding children
				foreach($inputs['priorities'] as $newPriority){
					$priority = new priority;
					$priority->parent = $usertype->id;
					$priority->child = $newPriority;
					$priority->save();
				}

				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("settings/usertypes/edit/".$usertype->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
			//refill the search form
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	/**
	* Geting a usertype based on recevied data from router
	* @param mixed[] $data
	* @throws NotFound if not found any thing
	* @return usertype
	*/
	private function getUserType($data){
		$types = authorization::childrenTypes();

		$usertype = new usertype();
		$usertype->with('permissions');
		$usertype->with('children');
		if($types){
			$usertype->where("id", $types, 'in');
		}else{
			$usertype->where("id", null, 'is');
		}
		$usertype->where("id", $data['type']);
		if(!$usertype = $usertype->getOne()){
			throw new NotFound;
		}
		return $usertype;
	}
	/**
	* Edit a usertype and it's permissions and relations
	* @param mixed[] $data
	* @uses usertype::getUserType()
	* @return \packages\base\response
	*/
	public function edit($data){
		authorization::haveOrFail('settings_usertypes_edit');
		$view = view::byName("\\packages\\userpanel\\views\\settings\\usertypes\\edit");
		$usertype = $this->getUserType($data);
		$user = authentication::getUser();
		if ($user->isManager()) {
			$allPermissions = permissions::get();
		} else {
			$allPermissions = $user->getPermissions();
		}
		$childrenTypes = authorization::childrenTypes();
		$usertypePermissions = array_column($usertype->toArray()['permissions'], 'name');
		$usertypePriorities = array_column($usertype->toArray()['children'], 'child');


		//pass data to view
		$view->setDataForm($usertype->title,'title');
		$view->setUserType($usertype);
		$view->setPermissions($allPermissions);
		$view->setChildrenTypes(usertype::where("id", $childrenTypes, 'in')->get());

		if(http::is_post()){
			$this->response->setStatus(false);
			$inputsRules = array(
				'title' => array(
					'type' => 'string',
					'optional' => true
				),
				'permissions' => array(),
				'priorities' => array(
					'optional' => true
				)
			);
			try{
				$inputs = $this->checkinputs($inputsRules);

				//Extra validation for permissions and comparing with list of total permissions
				if(is_array($inputs['permissions'])){
					foreach($inputs['permissions'] as $key => $permission){
						if(!in_array($permission, $allPermissions)){
							throw new inputValidation("permissions[{$key}]");
						}
					}
				}else{
					throw new inputValidation("permissions");
				}

				//Extra validation for priorities
				if(isset($inputs['priorities'])){
					if(is_array($inputs['priorities'])){
						foreach($inputs['priorities'] as $key => $priority){
							if(!in_array($priority,$childrenTypes)){
								throw new inputValidation("priorities[{$key}]");
							}
						}
					}else{
						throw new inputValidation("priorities");
					}
				}else{
					//setting an empty array makes all relations to delete
					$inputs['priorities'] = array();
				}

				//Update Permission's title
				$usertype->title = $inputs['title'];

				// Processing of deleting permissions
				$permissionsdelete = array_diff($usertypePermissions, $inputs['permissions']);
				if(!empty($permissionsdelete)){
					foreach($usertype->permissions as $permission){
						if(in_array($permission->name, $permissionsdelete)){
							$permission->delete();
						}
					}
				}

				// Processing of adding permissions
				$NewPermissions = array_diff($inputs['permissions'], $usertypePermissions);
				foreach($NewPermissions as $permissionName){
					$permission = new permission;
					$permission->type = $usertype->id;
					$permission->name = $permissionName;
					$permission->save();
				}

				// Processing of deleting children
				$prioritiesDelete = array_diff($usertypePriorities, $inputs['priorities']);
				// Processing of deleting priorities
				if(!empty($prioritiesDelete)){
					foreach($usertype->children as $child){
						if(in_array($child->child, $prioritiesDelete)){
							$child->delete();
						}
					}
				}

				// Processing of adding children
				$newPriorities = array_diff($inputs['priorities'], $usertypePriorities);
				foreach($newPriorities as $newPriority){
					$priority = new priority;
					$priority->parent = $usertype->id;
					$priority->child = $newPriority;
					$priority->save();
				}
				$usertype->save();

				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("settings/usertypes/edit/".$usertype->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	/**
	* Delete a usertype and it's permissions and relations
	* @param mixed[] $data
	* @uses usertype::getUserType()
	* @return \packages\base\response
	*/
	public function delete($data){
		authorization::haveOrFail('settings_usertypes_delete');
		$view = view::byName("\\packages\\userpanel\\views\\settings\\usertypes\\delete");
		$usertype = $this->getUserType($data);

		//pass data to view
		$view->setUserType($usertype);

		if(http::is_post()){
			try{
				$this->response->setStatus(false);
				if(!user::where("type", $usertype->id)->has()){
					$usertype->delete();
				}else{
					throw new UserTypeInUse();
				}
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url('settings/usertypes'));
			}catch(UserTypeInUse $e){
				$error = new error();
				$error->setCode('usertype.in_use');
				$view->addError($error);
			}
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
}
class UserTypeInUse extends \Exception{}
