<?php
namespace packages\userpanel\controllers\settings;
use packages\base\{response, NotFound};
use packages\userpanel\{controller, view, views, authorization, App, authentication};

class Apps extends controller {
	protected $authentication = true;
	public function search(): response {
		authorization::haveOrFail("apps_search");
		$view = view::byName(views\settings\apps\Search::class);
		$this->response->setView($view);
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
			"comparison" => array(
				"values" => array("equals", "startswith", "contains"),
				"default" => "contains",
				"optional" => true
			),
		));
		$types = authorization::childrenTypes();
		if (!$types) {
			unset($inputs["user"]);
		}
		$app = new App();
		$app->with("user");
		foreach (array("id", "name", "token") as $item) {
			if (isset($inputs[$item]) and $inputs[$item]) {
				$comparison = $inputs["comparison"];
				if(in_array($item, array("id"))){
					$comparison = "equals";
				}
				$app->where("userpanel_apps.{$item}", $inputs[$item], $comparison);
			}
		}
		if (isset($inputs["search_user"]) and $inputs["search_user"]) {
			$app->where("userpanel_apps.user", $inputs["search_user"]);
		}
		if ($types) {
			$app->where("userpanel_users.type", $types, "IN");
		} else {
			$app->where("userpanel_apps.user", authentication::getID());
		}
		$app->orderBy("userpanel_apps.id", "DESC");
		$app->pageLimit = $this->items_per_page;
		$apps = $app->paginate($this->page, array("userpanel_apps.*", "userpanel_users.*"));
		$view->setDataList($apps);
		$view->setPaginate($this->page, $app->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function add(): response {
		authorization::haveOrFail("apps_add");
		$inputs = $this->checkinputs(array(
			"name" => array(
				"type" => "string",
			),
			"token" => array(
				"type" => "string",
				"regex" => "/^[A-Za-z0-9]{10,32}$/"
			),
			"user" => array(
				"type" => "number",
				"optional" => true,
			),
		));
		$types = authorization::childrenTypes();
		if (!$types) {
			unset($inputs["user"]);
		}
		if (!isset($inputs["user"])) {
			$inputs["user"] = authentication::getID();
		}
		$app = new App();
		foreach (array("name", "token", "user") as $item) {
			$app->$item = $inputs[$item];
		}
		$app->save();
		$this->response->setStatus(true);
		return $this->response;
	}
	public function delete($data): response {
		$types = authorization::childrenTypes();
		$app = new App();
		$app->with("user");
		if ($types) {
			$app->where("userpanel_users.type", $types, "IN");
		} else {
			$app->where("userpanel_apps.user", authentication::getID());
		}
		$app->where("userpanel_apps.id", $data["app"]);
		if (!$app = $app->getOne(array("userpanel_apps.*", "userpanel_users.*"))) {
			throw new NotFound();
		}
		$app->delete();
		$this->response->setStatus(true);
		return $this->response;
	}
}
