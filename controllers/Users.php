<?php
namespace packages\userpanel\controllers;

use packages\base\{Validator\CellphoneValidator, db, IO\File, views\FormError, http, Image, InputValidation, InputValidationException, NotFound, Packages, Response, Translator};
use packages\base\db\{DuplicateRecord, InputDataType, Parenthesis};
use packages\userpanel;
use packages\userpanel\{Authentication, Authorization, Controller, Country, Date, Events, Log, controllers\Login, logs, events\settings as SettingsEvent, user\SocialNetwork, User, Usertype, View};

use themes\clipone\views;

class Users extends Controller {
	protected $authentication = true;
	
	public function search() {
		Authorization::haveOrFail("users_list");
		$view = View::byName(views\users\Search::class);
		$this->response->setView($view);
		$types = Authorization::childrenTypes();
		$view->setCountries((new Country)->get());
		if ($types) {
			$view->setUserTypes((new Usertype)->where("id", $types, "in")->get());
		}
		$inputs = $this->checkInputs(array(
			"id" => array(
				"type" => "number",
				"optional" => true,
			),
			"name" => array(
				"type" => "string",
				"optional" => true,
			),
			"lastname" => array(
				"type" => "string",
				"optional" => true,
			),
			"email" => array(
				"type" => "string",
				"optional" => true,
			),
			"cellphone" => array(
				"type" => "string",
				"optional" => true,
			),
			"type" => array(
				"type" => function ($data, $rule, $input) use ($types) {
					if (!is_string($data)) {
						throw new InputValidationException($input);
					}
					$selectedTypes = explode(",", $data);
					if (array_diff($selectedTypes, $types)) {
						throw new InputValidationException($input);
					}
					return $selectedTypes;
				},
				"optional" => true,
			),
			"online" => array(
				"type" => "bool",
				"optional" => true,
				"default" => false,
			),
			"status" => array(
				"type" => "number",
				"optional" => true,
				"values" => [User::active, User::deactive, User::suspend],
			),
			"city" => array(
				"optional" => true,
				"type" => "string",
			),
			"country" => array(
				"type" => Country::class,
				"optional" => true,
			),
			"word" => array(
				"type" => "string",
				"optional" => true,
			),
			"comparison" => array(
				"values" => ["equals", "startswith", "contains"],
				"default" => "contains",
				"optional" => true
			),
			"download" => array(
				"type" => "string",
				"values" => array("csv"),
				"optional" => true,
			),
			"lastonline_from" => array(
				"type" => "date",
				"unix" => true,
				"optional" => true,
			),
			"lastonline_to" => array(
				"type" => "date",
				"unix" => true,
				"optional" => true,
			),
		));
		if (isset($inputs["lastonline_from"], $inputs["lastonline_to"])) {
			if ($inputs["lastonline_from"] >= $inputs["lastonline_to"]) {
				throw new InputValidationException("lastonline_from");
			}
		}
		if ($inputs["online"]) {
			unset($inputs["lastonline_from"], $inputs["lastonline_to"]);
		}
		$model = new User();
		if ($types) {
			$model->where("type", $types, 'in');
		} else {
			$model->where("id", Authentication::getID());
		}
		if ($inputs["online"]) {
			$model->where("lastonline", Date::time() - User::onlineTimeout, ">=");
		}
		if (isset($inputs['type'])) {
			$view->setDataForm($inputs['type'], 'type-select');
			$model->where('type', $inputs['type'], 'IN');
		}
		if (isset($inputs["lastonline_from"])) {
			$model->where("lastonline", $inputs["lastonline_from"], ">=");
		}
		if (isset($inputs["lastonline_to"])) {
			$model->where("lastonline", $inputs["lastonline_to"], "<");
		}
		foreach(["id", "name", "lastname", "email", "cellphone", "status", "city", "country"] as $item) {
			if (!isset($inputs[$item])) {
				continue;
			}
			$comparison = $inputs["comparison"];
			if (in_array($item, ["id", "status", "country"])) {
				$comparison = "equals";
				if ($item == "country") {
					$inputs[$item] = $inputs[$item]->id;
				}
			}
			$model->where($item, $inputs[$item], $comparison);
		}
		if (isset($inputs["word"])) {
			$parenthesis = new db\Parenthesis();
			foreach (["name", "lastname", "email", "cellphone"] as $item) {
				if (isset($inputs[$item])) {
					continue;
				}
				$parenthesis->orWhere($item, $inputs["word"], $inputs["comparison"]);
			}
			$parenthesis->orWhere("CONCAT(`name`, ' ', `lastname`)", $inputs["word"], $inputs["comparison"]);
			$model->where($parenthesis);
		}
		if (isset($inputs["download"])) {
			$user = new user;
			$users = $user->get();
			$csv = t("packages.userpanel.user.csv.id") . ";" .
				t("packages.userpanel.user.csv.name") . ";" .
				t("packages.userpanel.user.csv.lastname") . ";" .
				t("packages.userpanel.user.csv.email") . ";" .
				t("packages.userpanel.user.csv.cellphone") . ";" .
				t("packages.userpanel.user.csv.type") . ";" .
				t("packages.userpanel.user.csv.phone") . ";" .
				t("packages.userpanel.user.csv.city") . ";" .
				t("packages.userpanel.user.csv.country") . ";" .
				t("packages.userpanel.user.csv.zip") . ";" .
				t("packages.userpanel.user.csv.address") . ";" .
				t("packages.userpanel.user.csv.web") . ";" .
				t("packages.userpanel.user.csv.lastonline") . ";" .
				t("packages.userpanel.user.csv.credit") . ";" .
				t("packages.userpanel.user.csv.registered_at") . ";" .
				t("packages.userpanel.user.csv.status") . ";\n";
			foreach ($users as $user) {
				$status = "";
				switch($user->status) {
					case user::active:
						$status = t("packages.userpanel.user.csv.status.active");
						break;
					case user::deactive:
						$status = t("packages.userpanel.user.csv.status.deactive");
						break;
					case user::suspend:
						$status = t("packages.userpanel.user.csv.status.suspend");
						break;
				}
				$csv .= $user->id . ";" .
						($user->name ? $user->name : "-"). ";" .
						($user->lastname ? str_replace(";", "", $user->lastname) : "-"). ";" .
						($user->email ? $user->email : "-"). ";" .
						($user->cellphone ? $user->cellphone : "-"). ";" .
						($user->type? $user->type->title: "-"). ";" .
						($user->phone ? $user->phone : "-"). ";" .
						($user->city ? $user->city : "-"). ";" .
						($user->country ? $user->country->name: "-"). ";" .
						($user->zip ? $user->zip : "-"). ";" .
						($user->address? $user->address: "-"). ";" .
						($user->web ? $user->web : "-"). ";" .
						($user->lastonline ? (Date::format("Y/m/d H:i:s", $user->lastonline)) : "-"). ";" .
						$user->credit. ";" .
						($user->registered_at ? (Date::format("Y/m/d H:i:s", $user->registered_at)) : "-"). ";" .
						$status. ";\n";
			}
			$tmp = new File\Tmp();
			$tmp->write($csv);
			$file = new response\File();
			$file->setLocation($tmp);
			$file->setSize($tmp->size());
			$file->setName("userpanel-users.csv");
			$file->setMimeType("text/csv", "utf-8");
			$this->response->setFile($file);
			$this->response->forceDownload();
		} else {
			$model->pageLimit = $this->items_per_page;
			$users = $model->paginate($this->page);
			$this->total_pages = $model->totalPages;

			$view->setDataList($users);
			$view->setPaginate($this->page, $model->totalCount, $this->items_per_page);
		}

		$this->response->setStatus(true);
		return $this->response;
	}
	/**
	 * Add new user from userpanel view
	 * needs userpanel_users_add permission
	 *
	 * @return Response
	 */
	public function add(): Response {
		Authorization::haveOrFail('users_add');
		$view = View::byName(views\users\Add::class);
		$this->response->setView($view);
		$view->setCountries(Country::get());
		$types = Authorization::childrenTypes();
		$view->setTypes($types ? (new Usertype)->where('id', $types, 'IN')->get() : []);
		$view->setDataForm(105, 'country');
		$this->response->setStatus(true);
		return $this->response;
	}
	/**
	 * Add new user to database
	 * needs userpanel_users_add permission
	 *
	 * @throws InputValidationException on wrong input
	 * @throws DuplicateRecord if user email or cellphone is exists in databse
	 * @return Response
	 */
	public function store(): Response {
		Authorization::haveOrFail('users_add');
		$view = View::byName(views\users\Add::class);
		$this->response->setView($view);
		$view->setDataForm(105, 'country');
		$view->setCountries(Country::get());
		$types = Authorization::childrenTypes();
		$view->setTypes($types ? (new Usertype)->where('id', $types, 'IN')->get() : []);

		$rules = array(
			'name' => array(
				'type' => 'string',
			),
			'lastname' => array(
				'type' => 'string',
				'optional' => true,
			),
			'email' => array(
				'type' => 'email',
			),
			'cellphone' => array(
				'type' => function($data, $rule, $input) {
					if (!preg_match("/^(\+)?\d+$/", $data)) {
						throw new InputValidationException($input);
					}
					return (new CellphoneValidator)->validate($input, $rule, $data);
				},
			),
			'password' => array(),
			'type' => array(
				'type' => 'number',
			),
			'zip' => array(
				'type' => 'number',
				'optional' => true,
			),
			'city' => array(
				'type' => 'string',
				'optional' => true,
			),
			'country' => array(
				'type' => Country::class,
				'optional' => true,
			),
			'address' => array(
				'type' => 'string',
				'optional' => true,
			),
			'phone' => array(
				'type' => 'string',
				'optional' => true,
			),
			'status' => array(
				'type' => 'number',
				'values' => array(User::active, User::deactive, User::suspend),
			),
			'socialnets' => array(
				'type' => function ($data, $rules, $input) {
					if (!$data) return [];
					if (!is_array($data)) throw new InputValidationException($input);

					foreach ($data as $network => $url) {
						$regex = "";
						switch ($network) {
							case (SocialNetwork::telegram):
								$regex = '/^(https?:\\/\\/(t(elegram)?\\.me|telegram\\.org)\\/)?(?<username>[a-z0-9\\_]{5,32})\\/?$/i';
								break;
							case (SocialNetwork::instagram):
								$regex = '/^(https?:\/\/(www\.)?instagram\.com\/)?(?<username>[A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/i';
								break;
							case (SocialNetwork::skype):
								$regex = '/^(?:(?:callto|skype):)?(?<username>(?:[a-z][a-z0-9\\.,\\-_]{5,31}))(?:\\?(?:add|call|chat|sendfile|userinfo))?$/i';
								$site = "skype:";
								break;
							case (SocialNetwork::twitter):
								$regex = '/^(?:https?:\/\/(?:.*\.)?twitter\.com\/)?(?<username>[A-z 0-9 _]+)\/?$/i';
								break;
							case (SocialNetwork::facebook):
								$regex = '/^(?:https?:\/\/(?:www\.)?(?:facebook|fb)\.com\/)?(?<username>[A-z 0-9 _ - \.]+)\/?$/i';
								break;
							case (SocialNetwork::gplus):
								$regex = '/^(?:https?:\/\/plus\.google\.com\/)?(?<username>(?:\+[a-z0-9\_]+|\d{21}))\/?$/i';
								break;
							default:
								throw new InputValidationException($input . "[{$network}]");
						}
						if ($url) {
							if (preg_match($regex, $url, $matches)) {
								$data[$network] = $matches['username'];
							} else {
								throw new InputValidationException($input . "[{$network}]");
							}
						}
					}
					return $data;
				},
				'optional' => true,
			),
		);
		if (Authorization::is_accessed('profile_edit_privacy')) {
			foreach ([
				'email',
				'cellphone',
				'phone',
				'socialnetworks_' . SocialNetwork::telegram,
				'socialnetworks_' . SocialNetwork::twitter,
				'socialnetworks_' . SocialNetwork::instagram,
				'socialnetworks_' . SocialNetwork::facebook,
				'socialnetworks_' . SocialNetwork::skype,
				'socialnetworks_' . SocialNetwork::gplus
			] as $visibility) {
				$inputs['visibility_' . $visibility] = array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true,
				);
			}
		}
		if (Authorization::is_accessed("users_edit_credit")) {
			$rules["credit"] = array(
				"optional" => true,
				"type" => "number",
				"zero" => true,
				"min" => 0,
			);
		}
		$view->setDataForm($this->inputsValue($rules));
		$inputs = $this->checkInputs($rules);

		$user = new User($inputs);
		$user->password_hash($inputs['password']);
		unset($inputs['password']);
		$user->save();
		if (isset($inputs['socialnets']) and $inputs['socialnets']) {
			foreach ($inputs['socialnets'] as $network => $username) {
				if ($username) {
					$socialnet = new SocialNetwork();
					$socialnet->user = $user->id;
					$socialnet->network = $network;
					$socialnet->username = $username;
					$socialnet->save();
				}
			}
		}
		if (Authorization::is_accessed('profile_edit_privacy')) {
			$visibilities = array();
			foreach(array(
				'email',
				'cellphone',
				'phone',
				'socialnetworks_' . SocialNetwork::telegram,
				'socialnetworks_' . SocialNetwork::instagram,
				'socialnetworks_' . SocialNetwork::skype,
				'socialnetworks_' . SocialNetwork::twitter,
				'socialnetworks_' . SocialNetwork::facebook,
				'socialnetworks_' . SocialNetwork::gplus,
			) as $field) {
				if (array_key_exists('visibility_' . $field, $inputs) and $inputs['visibility_' . $field]) {
					$visibilities[] = $field;
				}
			}
			$user->setOption('visibilities', $visibilities);
		}

		$log = new Log();
		$log->title = t('log.userAdd', ['user_name' => $user->getFullName(), 'user_id' => $user->id]);
		$log->type = logs\Register::class;
		$log->user = Authentication::getID();
		$log->parameters = [
			'user' => $user,
			'inputs' => $inputs,
		];
		$log->save();
		$this->response->setStatus(true);
		$this->response->go(userpanel\url("users/edit/{$user->id}"));
		return $this->response;
	}
	public function view($data){
		authorization::haveOrFail('users_view');
		$user = user::with('type')->with('socialnetworks')->byId($data['user']);
		if(!$user){
			throw new NotFound;
		}
		$settingsEvent = new settingsEvent();
		$settingsEvent->setUser($user);
		$settingsEvent->trigger();
		$view = View::byName(views\users\View::class);
		$view->setData($user, "user");
		$view->triggerTabs();
		$view->activeTab("view");
		$view->setSettings($settingsEvent->get());
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('users_edit');
		$types = authorization::childrenTypes();
		if (!$types) {
			throw new NotFound();
		}
		$user = user::where("id", $data['user'])->where("type", $types, 'in')->getOne();
		if(!$user){
			throw new NotFound();
		}
		$settingsEvent = new settingsEvent();
		$settingsEvent->setUser($user);
		$settingsEvent->trigger();
		$view = View::byName(views\users\View::class);
		$view->setData($user, "user");
		$view->triggerTabs();
		$view->activeTab("edit");
		$view->setTypes(usertype::where("id", $types, 'in')->get());
		$view->setCountries(country::get());
		$view->setUserData($user);
		$view->setSettings($settingsEvent->get());
		if(http::is_post()){
			$inputs = array(
				'name' => array(
					'optional' => true,
					'type' => 'string'
				),
				'lastname' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'email' => array(
					'type' => 'email',
					'optional' => true,
				),
				'cellphone' => array(
					'type' => function($data, $rule) {
						if (!preg_match("/^(\+)?\d+$/", $data)) {
							throw new InputValidationException("cellphone");
						}
						$validator = new CellphoneValidator;
						return $validator->validate("cellphone", $rule, $data);
					},
					'optional' => true
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
					'empty' => true,
					'type' => 'number'
				),
				'city' => array(
					'optional' => true,
					'empty' => true,
					'type' => 'string'
				),
				'country' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true,
				),
				'address' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true,
				),
				'phone' => array(
					'optional' => true,
					'type' => 'string',
					'empty' => true
				),
				'status' => array(
					'type' => 'number',
					'optional' => true,
					'values' => array(user::active, user::deactive,user::suspend),
					'empty' => true
				),
				'avatar' => array(
					'optional' => true,
					'type' => 'file',
					'empty' => true,
				),
				'socialnets' => array(
					'optional' => true
				),
				'visibility_email' => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_cellphone' => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_phone' => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::twitter => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::twitter => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::facebook => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::skype => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::gplus => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::instagram => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'visibility_socialnetworks_'.socialnetwork::telegram => array(
					'optional' => true,
					'type' => 'bool',
					'empty' => true
				),
				'avatar_remove' => [
					'type' => 'bool',
					'optional' => true
				]
			);
			if (Authorization::is_accessed("users_edit_credit")) {
				$inputs["credit"] = array(
					"optional" => true,
					"type" => "int",
					"empty" => true,
				);
			}
			$this->response->setStatus(false);
			$oldData = $user->original_data;
			unset($oldData["id"], $oldData["lastonline"], $oldData["remember_token"]);
			$formdata = $this->checkinputs($inputs);
			if(isset($formdata['type']) and !usertype::byId($formdata['type'])){
				throw new InputValidationException("type");
			}
			if(isset($formdata['country'])){
				if(!country::byId($formdata['country'])){
					throw new InputValidationException("country");
				}
			}
			if(isset($formdata['socialnets'])){
				foreach($formdata['socialnets'] as $network => $url){
					switch($network){
						case(socialnetwork::telegram):
							$regex = '/^(https?:\\/\\/(t(elegram)?\\.me|telegram\\.org)\\/)?(?<username>[a-z0-9\\_]{5,32})\\/?$/i';
							break;
						case(socialnetwork::instagram):
							$regex = '/^(https?:\/\/(www\.)?instagram\.com\/)?(?<username>[A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/i';
							break;
						case(socialnetwork::skype):
							$regex = '/^(?:(?:callto|skype):)?(?<username>(?:[a-z][a-z0-9\\.,\\-_]{5,31}))(?:\\?(?:add|call|chat|sendfile|userinfo))?$/i';
							$site = "skype:";
							break;
						case(socialnetwork::twitter):
							$regex = '/^(?:https?:\/\/(?:.*\.)?twitter\.com\/)?(?<username>[A-z 0-9 _]+)\/?$/i';
							break;
						case(socialnetwork::facebook):
							$regex = '/^(?:https?:\/\/(?:www\.)?(?:facebook|fb)\.com\/)?(?<username>[A-z 0-9 _ - \.]+)\/?$/i';
							break;
						case(socialnetwork::gplus):
							$regex = '/^(?:https?:\/\/plus\.google\.com\/)?(?<username>(?:\+[a-z0-9\_]+|\d{21}))\/?$/i';
							break;
						default:
							throw new InputValidationException("socialnets[{$network}]");
					}
					if($url){
						if(preg_match($regex, $url, $matches)){
							$formdata['socialnets'][$network] = $matches['username'];
						}else{
							throw new InputValidationException("socialnets[{$network}]");
						}
					}
				}
			}

			if(isset($formdata['avatar'])){
				if($formdata['avatar']['error'] == 0){
					$type = getimagesize($formdata["avatar"]['tmp_name']);
					if(!in_array($type[2], array(IMAGETYPE_JPEG ,IMAGETYPE_GIF, IMAGETYPE_PNG))){
						throw new InputValidationException("avatar");
					}
				}elseif($formdata['avatar']['error'] == 4){
					unset($formdata['avatar']);
				}elseif(isset($formdata['avatar']['error'])){
					throw new InputValidationException("avatar");
				}
			}
			if(isset($formdata['avatar'])){
				$file = new file\local($formdata['avatar']['tmp_name']);
				$tmpfile = new file\tmp();
				$type = getimagesize($file->getPath());
				switch($type[2]){
					case(IMAGETYPE_JPEG):
						$image = new image\jpeg($file);
						$type_name = 'jpg';
						break;
					case(IMAGETYPE_GIF):
						$image = new image\gif($file);
						$type_name = 'gif';
						break;
					case(IMAGETYPE_PNG):
						$image = new image\png($file);
						$type_name = 'png';
						break;
				}
				$image->resize(200, 200)->saveToFile($tmpfile);
				$formdata['avatar'] = 'sotrage/public_avatar/'.$tmpfile->md5().'.'.$type_name;
				$avatar = new file\local(packages::package('userpanel')->getFilePath($formdata['avatar']));
				$avatar->getDirectory()->make(true);
				$tmpfile->copyTo($avatar);
			}
			if(!isset($formdata['avatar']) or !is_string($formdata['avatar'])){
				unset($formdata['avatar']);
			}
			if(isset($formdata['avatar_remove']) and $formdata['avatar_remove']){
				$formdata['avatar'] = null;
			}
			if(isset($formdata['password']) and $formdata['password']){
				$user->password_hash($formdata['password']);
			}
			unset($formdata['password']);
			$user->save($formdata);
			unset($formdata['avatar']);
			if(isset($formdata['socialnets'])){
				foreach($formdata['socialnets'] as $network => $username){
					if($username){
						$edited = false;
						foreach($user->socialnetworks as $socialnet){
							if($socialnet->network == $network){
								$edited = true;
								$socialnet->username = $username;
								$socialnet->save();
								break;
							}
						}
						if(!$edited){
							$socialnet = new socialnetwork();
							$socialnet->user = $user->id;
							$socialnet->network = $network;
							$socialnet->username = $username;
							$socialnet->save();
						}
					}else{
						foreach($user->socialnetworks as $socialnet){
							if($socialnet->network == $network){
								$socialnet->delete();
								break;
							}
						}
					}
				}
			}
			$inputs = array(
				"oldData" => array(),
				"newData" => array()
			);
			if(authorization::is_accessed('profile_edit_privacy')){
				$visibilities = $user->getOption("visibilities");
				if(!is_array($visibilities)){
					$visibilities = array();
				}
				foreach (array(
					"email",
					"cellphone",
					"phone",
					"socialnetworks_" . socialnetwork::telegram,
					"socialnetworks_" . socialnetwork::instagram,
					"socialnetworks_" . socialnetwork::skype,
					"socialnetworks_" . socialnetwork::twitter,
					"socialnetworks_" . socialnetwork::facebook,
					"socialnetworks_" . socialnetwork::gplus,
				) as $field) {
					$item = "visibility_" . $field;
					if (array_key_exists($item, $formdata)) {
						if ($formdata[$item]) {
							if (!in_array($field, $visibilities)) {
								$inputs["newData"]["visibilities"][] = $field;
							}
							$visibilities[] = $field;
						} else if (($key = array_search($field, $visibilities)) !== false) {
							$inputs["oldData"]["visibilities"][] = $field;
							unset($visibilities[$key]);
						}
					}
				}
				$visibilities = array_values(array_unique($visibilities));
				$user->setOption("visibilities", $visibilities);
			}
			foreach ($oldData as $field => $val) {
				$newVal = $user->original_data[$field];
				if ($val != $newVal) {
					$inputs["oldData"][$field] = $val ? $val : "-";
					$inputs["newData"][$field] = $newVal;
				}
			}
			if (isset($inputs["oldData"]["password"])) {
				$inputs["oldData"]["password"] = "********";
			}
			if (isset($inputs["newData"]["password"])) {
				$inputs["newData"]["password"] = "********";
			}
			$actionUser = authentication::getUser();
			if($actionUser->id == $user->id){
				$log = new log();
				$log->title = t("log.profileEdit");
				$log->type = logs\userEdit::class;
				$log->user = $user->id;
				$log->parameters = $inputs;
				$log->save();
			}else{
				$log = new log();
				$log->title = t("log.userEdit", ['user_name' => $user->getFullName(), 'user_id' => $user->id]);
				$log->type = logs\userEdit::class;
				$log->user = $actionUser->id;
				$log->parameters = array_merge(array(
					"editedUser" => $user->id,
				), $inputs);
				$log->save();

				$log = new log();
				$log->title = t("log.editedYou", ['user_name' => $actionUser->getFullName(), "user_id" => $actionUser->id]);
				$log->type = logs\userEdit::class;
				$log->user = $user->id;
				$log->parameters = $inputs;
				$log->save();
			}
			$this->response->setStatus(true);
			$view->setDataForm($user->toArray());
		}else{
			$this->response->setStatus(true);
		}
		$view->setForm();
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('users_delete');
		$types = authorization::childrenTypes();
		if (!$types) {
			throw new NotFound();
		}
		$user = user::where("id", $data['user'])->where("type", $types, 'in')->getOne();
		$actionUser = authentication::getUser();
		if(!$user or $actionUser->id == $user->id){
			throw new NotFound;
		}
		$view = view::byName("\\packages\\userpanel\\views\\users\\delete");
		if(http::is_post()){
			$log = new log();
			$log->title = t("log.userDelete", ['user_name' => $user->getFullName(), 'user_id' => $user->id]);
			$log->type = logs\userDelete::class;
			$log->user = $actionUser->id;
			$log->parameters = ['user' => $user];
			$log->save();

			$user->delete();
			$this->response->setStatus(true);
			$this->response->go(userpanel\url('users'));
		}else{
			$this->response->setStatus(true);
			$view->setDataForm($user->toArray());
			$this->response->setView($view);
		}
		return $this->response;
	}
	public function settings($data){
		authorization::haveOrFail('users_settings');
		$types = authorization::childrenTypes();
		if (!$types) {
			throw new NotFound();
		}
		$user = user::where("id", $data['user'])->where("type", $types, 'in')->getOne();
		if(!$user->id){
			throw new NotFound();
		}
		$settingsEvent = new settingsEvent();
		$settingsEvent->setUser($user);
		$settingsEvent->trigger();
		if(!$settingsEvent->get()){
			throw new NotFound();
		}
		$view = View::byName(views\users\View::class);
		$view->setData($user, "user");
		$view->triggerTabs();
		$view->activeTab("settings");
		$view->setSettings($settingsEvent->get());
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function change($data){
		authorization::haveOrFail('users_settings');
		$types = authorization::childrenTypes();
		if (!$types) {
			throw new NotFound();
		}
		$user = user::where("id", $data['user'])->where("type", $types, 'in')->getOne();
		if(!$user->id){
			throw new NotFound();
		}
		$settingsEvent = new settingsEvent();
		$settingsEvent->setUser($user);
		$settingsEvent->trigger();
		$settings = $settingsEvent->get();
		if(!$settings){
			throw new NotFound();
		}
		$actionUser = authentication::getUser();

		$view = View::byName(views\users\View::class);
		$view->setData($user, "user");
		$view->triggerTabs();
		$view->activeTab("settings");
		$this->response->setView($view);
		$view->setSettings($settings);
		$this->response->setStatus(false);
		$inputsRules = [];
		$logs = array();
		foreach ($settings as $setting) {
			if ($SRules = $setting->getInputs()) {
				$SRules = $inputsRules = array_merge($inputsRules, $SRules);
				$ginputs = $this->checkinputs($SRules);
				$logs = array_merge($logs, $setting->store($ginputs, $user));
			}
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$this->response->setStatus(true);
		$inputs = array(
			"oldData" => array(),
			"newData" => array(),
		);
		foreach ($logs as $log) {
			$inputs["oldData"][$log->getName()] = array("title" => $log->getTitle(), "value" => $log->getOldValue());
			$inputs["newData"][$log->getName()] = array("title" => $log->getTitle(), "value" => $log->getNewValue());
		}
		if ($logs) {
			if ($actionUser->id == $user->id) {
				$log = new log();
				$log->title = t("log.profileEdit");
				$log->type = logs\userEdit::class;
				$log->user = $user->id;
				$log->parameters = $inputs;
				$log->save();
			} else {
				$log = new log();
				$log->title = t("log.userEdit", ['user_name' => $user->getFullName(), 'user_id' => $user->id]);
				$log->type = logs\userEdit::class;
				$log->user = $actionUser->id;
				$log->parameters = array_merge(array(
					"editedUser" => $user->id,
				), $inputs);
				$log->save();

				$log = new log();
				$log->title = t("log.editedYou", ['user_name' => $actionUser->getFullName(), "user_id" => $actionUser->id]);
				$log->type = logs\userEdit::class;
				$log->user = $user->id;
				$log->parameters = $inputs;
				$log->save();
			}
		}
		return $this->response;
	}
	public function activate($data) {
		authorization::haveOrFail('users_edit');
		$user = User::byId($data['user']);
		if ($user->status == User::active) {
			throw new NotFound();
		}
		$logparameter = array(
			"oldData" => array(
				"status" => $user->status,
			),
			"newData" => array(
				"status" => User::active,
			),
		);
		$user->status = User::active;
		$user->save();
		$actionUser = Authentication::getUser();
		$log = new Log();
		$log->title = t("log.userEdit", ['user_name' => $user->getFullName(), 'user_id' => $user->id]);
		$log->type = Logs\userEdit::class;
		$log->user = $actionUser->id;
		$log->parameters = array_merge(array(
			"editedUser" => $user->id,
		), $logparameter);
		$log->save();

		$log = new Log();
		$log->title = t("log.editedYou", ['user_name' => $actionUser->getFullName(), "user_id" => $actionUser->id]);
		$log->type = Logs\userEdit::class;
		$log->user = $user->id;
		$log->parameters = $logparameter;
		$log->save();

		$event = new Events\Users\Activate($user);
		$event->trigger();
		
		$this->response->setStatus(true);
		return $this->response;
	}
	public function suspend($data) {
		authorization::haveOrFail('users_edit');
		$user = User::byId($data['user']);
		if ($user->status == User::suspend) {
			throw new NotFound();
		}
		$logparameter = array(
			"oldData" => array(
				"status" => $user->status,
			),
			"newData" => array(
				"status" => User::suspend,
			),
		);
		$user->status = User::suspend;
		$user->save();
		$actionUser = Authentication::getUser();
		$log = new Log();
		$log->title = t("log.userEdit", ['user_name' => $user->getFullName(), 'user_id' => $user->id]);
		$log->type = Logs\userEdit::class;
		$log->user = $actionUser->id;
		$log->parameters = array_merge(array(
			"editedUser" => $user->id,
		), $logparameter);
		$log->save();

		$log = new Log();
		$log->title = t("log.editedYou", ['user_name' => $actionUser->getFullName(), "user_id" => $actionUser->id]);
		$log->type = Logs\userEdit::class;
		$log->user = $user->id;
		$log->parameters = $logparameter;
		$log->save();

		$event = new Events\Users\Suspend($user);
		$event->trigger();
		
		$this->response->setStatus(true);
		return $this->response;
	}
	public function loginAsUser($data) {
		authorization::haveOrFail('users_login');
		$user = user::byId($data['user']);

		Login::doLogin($user);

		$this->response->setStatus(true);
		$this->response->go(userpanel\url());
		return $this->response;
	}
}
