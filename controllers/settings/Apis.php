<?php
namespace packages\userpanel\controllers\settings;
use packages\base\{response, NotFound, inputValidation};
use packages\userpanel\{controller, view, views, authorization, App, user\Api, authentication};

class Apis extends controller {
	protected $authentication = true;
	public function search(): response {
		authorization::haveOrFail("apis_search");
		$view = view::byName(views\settings\apis\Search::class);
		$this->response->setView($view);
		$types = authorization::childrenTypes();
		$app = new App();
		$app->with("user");
		if ($types) {
			$app->where("userpanel_users.type", $types, "IN");
		} else {
			$app->where("userpanel_apps.user", authentication::getID());
		}
		$view->setApps($app->get(null, array("userpanel_apps.*", "userpanel_users.*")));
		$inputs = $this->checkinputs(array(
			"id" => array(
				"type" => "number",
				"empty" => true,
				"optional" => true,
			),
			"search_user" => array(
				"type" => "number",
				"empty" => true,
				"optional" => true,
			),
			"name" => array(
				"type" => "string",
				"empty" => true,
				"optional" => true,
			),
			"token" => array(
				"type" => "string",
				"empty" => true,
				"optional" => true,
			),
			"app" => array(
				"type" => "number",
				"empty" => true,
				"optional" => true,
			),
			"status" => array(
				"values" => array(Api::active, Api::disable),
				"empty" => true,
				"optional" => true,
			),
			"comparison" => array(
				"values" => array("equals", "startswith", "contains"),
				"default" => "contains",
				"optional" => true
			),
		));
		if (!$types) {
			unset($inputs["user"]);
		}
		$api = new Api();
		$api->with("user");
		$api->with("app");
		foreach (array("id", "app", "token", "status") as $item) {
			if (isset($inputs[$item]) and $inputs[$item]) {
				$comparison = $inputs["comparison"];
				if(in_array($item, array("id", "app", "status"))){
					$comparison = "equals";
				}
				$api->where("userpanel_users_apis.{$item}", $inputs[$item], $comparison);
			}
		}
		if (isset($inputs["search_user"]) and $inputs["search_user"]) {
			$api->where("userpanel_users_apis.user", $inputs["search_user"]);
		}
		if ($types) {
			$api->where("userpanel_users.type", $types, "IN");
		} else {
			$api->where("userpanel_users_apis.user", authentication::getID());
		}
		$api->orderBy("userpanel_users_apis.id", "DESC");
		$api->pageLimit = $this->items_per_page;
		$apis = $api->paginate($this->page, array("userpanel_users_apis.*", "userpanel_users.*", "userpanel_apps.*"));
		$view->setDataList($apis);
		$view->setPaginate($this->page, $api->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function add(): response {
		authorization::haveOrFail("apis_add");
		$inputs = $this->checkinputs(array(
			"app" => array(
				"type" => "number",
			),
			"token" => array(
				"type" => "string",
				"regex" => "/^[A-Za-z0-9]{10,32}$/"
			),
			"user" => array(
				"type" => "number",
				"optional" => true,
				"default" => authentication::getID(),
			),
			"status" => array(
				"values" => array(Api::active, Api::disable),
				"optional" => true,
				"default" => Api::active,
			),
		));
		if (!authorization::childrenTypes()) {
			$inputs["user"] = authentication::getID();
		}
		if (!App::byId($inputs["app"])) {
			throw new inputValidation("app");
		}
		$api = new Api();
		foreach (array("app", "token", "user", "status") as $item) {
			$api->$item = $inputs[$item];
		}
		$api->save();
		$this->response->setStatus(true);
		return $this->response;
	}
	public function edit($data): response {
		authorization::haveOrFail("apis_edit");
		$types = authorization::childrenTypes();
		$api = new Api();
		$api->with("user");
		if ($types) {
			$api->where("userpanel_users.type", $types, "IN");
		} else {
			$api->where("userpanel_users_apis.user", authentication::getID());
		}
		$api->where("userpanel_users_apis.id", $data["api"]);
		if (!$api = $api->getOne(array("userpanel_users_apis.*", "userpanel_users.*"))) {
			throw new NotFound();
		}
		$inputs = $this->checkinputs(array(
			"edit_app" => array(
				"type" => "number",
				"optional" => true,
			),
			"edit_token" => array(
				"type" => "string",
				"regex" => "/^[A-Za-z0-9]{10,32}$/",
				"optional" => true,
			),
			"edit_user" => array(
				"type" => "number",
				"optional" => true,
			),
			"edit_status" => array(
				"values" => array(Api::active, Api::disable),
				"optional" => true,
			),
		));
		if (!$types) {
			unset($inputs["user"]);
		}
		if (isset($inputs["edit_app"]) and !App::byId($inputs["edit_app"])) {
			throw new inputValidation("app");
		}
		foreach (array("edit_app", "edit_token", "edit_user", "edit_status") as $item) {
			if (isset($inputs[$item])) {
				$api->{substr($item, 5)} = $inputs[$item];
			}
		}
		$api->save();
		$this->response->setStatus(true);
		return $this->response;
	}
	public function delete($data): response {
		$types = authorization::childrenTypes();
		$api = new Api();
		$api->with("user");
		if ($types) {
			$api->where("userpanel_users.type", $types, "IN");
		} else {
			$api->where("userpanel_users_apis.user", authentication::getID());
		}
		$api->where("userpanel_users_apis.id", $data["api"]);
		if (!$api = $api->getOne(array("userpanel_users_apis.*", "userpanel_users.*"))) {
			throw new NotFound();
		}
		$api->delete();
		$this->response->setStatus(true);
		return $this->response;
	}
}
