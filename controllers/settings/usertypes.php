<?php
namespace packages\userpanel\controllers\settings;

use packages\base\{db, Http, InputValidationException, NotFound, Options, Response, View};
use packages\userpanel\{Authentication, Authorization, AuthorizationException, Controller, User, Usertype, views, validators};
use packages\userpanel\usertype\{Permission, Permissions, Priority};
use function packages\userpanel\url;

class Usertypes extends Controller {
	private static function getUserType($data): Usertype {
		$types = Authorization::childrenTypes();
		if (!$types or !in_array($data["type"], $types)) {
			throw new NotFound();
		}
		$model = (new Usertype())
			->with("permissions")
			->with("children")
			->where("id", $data["type"])
			->getOne();
		if (!$model) {
			throw new NotFound;
		}
		return $model;
	}

	/**
	* @var bool require authentication
	*/
	protected $authentication = true;

	public function listTypes() {
		Authorization::haveOrFail("settings_usertypes_list");
		$view = View::byName(views\settings\usertypes\ListView::class);
		$this->response->setView($view);

		$types = Authorization::childrenTypes();

		$inputs = $this->checkinputs(array(
			"id" => array(
				"type" => "number",
				"optional" => true,
				"values" => $types
			),
			"title" => array(
				"type" => "string",
				"optional" => true,
			),
			"word" => array(
				"type" => "string",
				"optional" => true,
			),
			"comparison" => array(
				"values" => array("equals", "startswith", "contains"),
				"default" => "contains",
				"optional" => true
			)
		));

		$model = new Usertype();
		if ($types) {
			$model->where("id", $types, "in");
		} else {
			$model->where("id", null, "is");
		}
		foreach (array("id", "title") as $item) {
			if (isset($inputs[$item])) {
				$comparison = $inputs["comparison"];
				if(in_array($item, array("id"))){
					$comparison = "equals";
				}
				$model->where($item, $inputs[$item], $comparison);
			}
		}

		if (isset($inputs["word"]) and $inputs["word"]) {
			$parenthesis = new db\Parenthesis();
			foreach(array("title") as $item){
				if(!isset($inputs[$item]) or !$inputs[$item]){
					$parenthesis->where($item,$inputs["word"], $inputs["comparison"], "OR");
				}
			}
			$model->where($parenthesis);
		}

		$model->pageLimit = $this->items_per_page;
		$models = $model->paginate($this->page);
		$this->total_pages = $model->totalPages;
		$view->setDataList($models);
		$view->setPaginate($this->page, $model->totalCount, $this->items_per_page);

		$this->response->setStatus(true);
		return $this->response;
	}

	public function view($data): Response {
		if (!Authorization::is_accessed("users_edit_permissions") and !Authorization::is_accessed("settings_usertypes_edit")) {
			throw new AuthorizationException("userpanel_usertypes");
		}
		$usertype = self::getUserType($data);
		$this->response->setData($usertype->toArray(), "usertype");
		$me = Authentication::getUser();
		$allPermissions = Permissions::existentForUser($me);
		$this->response->setData($allPermissions, "all_permissions");
		$this->response->setStatus(true);
		return $this->response;
	}

	public function add() {
		Authorization::haveOrFail("settings_usertypes_add");
		$view = View::byName(views\settings\usertypes\Add::class);
		$this->response->setView($view);

		$me = Authentication::getUser();
		if ($me->isManager()) {
			$allPermissions = Permissions::get();
		} else {
			$allPermissions = $me->getPermissions();
		}
		$childrenTypes = Authorization::childrenTypes();

		//pass data to view
		$view->setPermissions($allPermissions);
		$view->setChildrenTypes($childrenTypes ? (new Usertype)->where("id", $childrenTypes, "in")->get() : array());

		if (Http::is_post()) {
			$this->response->setStatus(false);
			$inputs = $this->checkinputs( array(
				"title" => array(
					"type" => "string",
					"optional" => true
				),
				"permissions" => array(
					"type" => validators\UsertypePermissionsValidator::class,
					"values" => $allPermissions,
				),
				"priorities" => array(
					"type" => validators\UsertypePrioritiesValidator::class,
					"optional" => true,
					"default" => [],
					"values" => $childrenTypes,
				)
			));

			//save new usertype
			$usertype = new Usertype();
			$usertype->title = $inputs["title"];
			$usertype->save();

			//add as child for user"s usertype
			$parentTypes = $me->parentTypes();
			$parentTypes[] = $me->type->id;
			foreach (array_unique($parentTypes) as $type) {
				$priority = new Priority;
				$priority->parent = $type;
				$priority->child = $usertype->id;
				$priority->save();
			}

			// Processing of adding permissions
			foreach($inputs["permissions"] as $permissionName){
				$permission = new Permission;
				$permission->type = $usertype->id;
				$permission->name = $permissionName;
				$permission->save();
			}

			// Processing of adding children
			foreach($inputs["priorities"] as $newPriority){
				$priority = new Priority;
				$priority->parent = $usertype->id;
				$priority->child = $newPriority;
				$priority->save();
			}

			$this->response->setStatus(true);
			$this->response->Go(url("settings/usertypes/edit/".$usertype->id));
		}else{
			$this->response->setStatus(true);
		}
		return $this->response;
	}

	public function edit($data){
		Authorization::haveOrFail("settings_usertypes_edit");
		$usertype = self::getUserType($data);
	
		$view = View::byName(views\settings\usertypes\Edit::class);
		$this->response->setView($view);
	
		$user = Authentication::getUser();
		if ($user->isManager()) {
			$allPermissions = Permissions::get();
		} else {
			$allPermissions = $user->getPermissions();
		}
	
		$childrenTypes = Authorization::childrenTypes();
		$usertypePermissions = array_column($usertype->toArray()["permissions"], "name");
		$usertypePriorities = array_column($usertype->toArray()["children"], "child");


		//pass data to view
		$view->setDataForm($usertype->title,"title");
		$view->setUserType($usertype);
		$view->setPermissions($allPermissions);
		$view->setChildrenTypes((new Usertype)->where("id", $childrenTypes, "in")->get());

		if (Http::is_post()) {
			$inputs = $this->checkinputs(array(
				"title" => array(
					"type" => "string",
					"optional" => true
				),
				"permissions" => array(
					"type" => validators\UsertypePermissionsValidator::class,
					"values" => $allPermissions,
				),
				"priorities" => array(
					"type" => validators\UsertypePrioritiesValidator::class,
					"optional" => true,
					"default" => [],
					"values" => $childrenTypes,
				)
			));

			//Update Permission"s title
			$usertype->title = $inputs["title"];
			
			//have Permission if not shown
			$disablepermissions = Options::get("packages.userpanel.disabledpermisions");
			if (!$disablepermissions) {
				$disablepermissions = array();
			}
			foreach($disablepermissions as $dp) {
				if (in_array($dp, $usertypePermissions)) {
					$inputs["permissions"][] = $dp;
				}
			}
			// Processing of deleting permissions
			$permissionsdelete = array_diff($usertypePermissions, $inputs["permissions"]);
			if (!empty($permissionsdelete)) {
				foreach ($usertype->permissions as $permission) {
					if (in_array($permission->name, $permissionsdelete)) {
						$permission->delete();
					}
				}
			}

			// Processing of adding permissions
			$NewPermissions = array_diff($inputs["permissions"], $usertypePermissions);
			foreach ($NewPermissions as $permissionName) {
				$permission = new permission;
				$permission->type = $usertype->id;
				$permission->name = $permissionName;
				$permission->save();
			}

			// Processing of deleting children
			$prioritiesDelete = array_diff($usertypePriorities, $inputs["priorities"]);
			// Processing of deleting priorities
			if(!empty($prioritiesDelete)){
				foreach($usertype->children as $child){
					if(in_array($child->child, $prioritiesDelete)){
						$child->delete();
					}
				}
			}

			// Processing of adding children
			$newPriorities = array_diff($inputs["priorities"], $usertypePriorities);
			foreach ($newPriorities as $newPriority) {
				$priority = new priority;
				$priority->parent = $usertype->id;
				$priority->child = $newPriority;
				$priority->save();
			}
			$usertype->save();

			$this->response->setStatus(true);
			$this->response->Go(url("settings/usertypes/edit/".$usertype->id));
		} else {
			$this->response->setStatus(true);
		}
		return $this->response;
	}

	public function delete($data) {
		Authorization::haveOrFail("settings_usertypes_delete");
		$usertype = self::getUserType($data);

		$view = View::byName(views\settings\usertypes\Delete::class);
		$this->response->setView($view);
		$view->setUserType($usertype);

		if (Http::is_post()) {
			$this->response->setStatus(false);
			if(!(new User)->where("type", $usertype->id)->has()){
				$usertype->delete();
			} else {
				throw new View\Error("usertype.in_use");
			}
			$this->response->setStatus(true);
			$this->response->Go(url("settings/usertypes"));
		} else {
			$this->response->setStatus(true);
		}
		return $this->response;
	}
}
