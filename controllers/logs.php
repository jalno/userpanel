<?php
namespace packages\userpanel\controllers;

use packages\base;
use packages\base\{db, db\parenthesis, InputValidation, db\duplicateRecord, Response};
use packages\userpanel;
use packages\userpanel\{Date, User, Authorization, Authentication, View, views, Log, Controller, AuthorizationException};

class Logs extends Controller {
	protected $authentication = true;
	
	/**
	 * Works with one of logs_search, users_view or profile_view permissions.
	 * 
	 * @todo Add IP search field.
	 * @return Response
	 */
	public function search() {
		$logs_permission = Authorization::is_accessed('logs_search');
		$users_permission = false;
		$profile_permission = false;
		if (!$logs_permission) {
			$users_permission = Authorization::is_accessed('users_view');
		}
		if (!$logs_permission and !$users_permission) {
			$profile_permission = Authorization::is_accessed('profile_view');
		}
		if (!$logs_permission and !$users_permission and !$profile_permission) {
			throw new AuthorizationException("userpanel_logs_search");
		}

		$view = View::byName(views\logs\search::class);
		$this->response->setView($view);
		$this->response->setStatus(false);

		$me = Authentication::getUser();
		$children = ($logs_permission or $users_permission) ? Authorization::childrenTypes() : [];

		$inputs = $this->checkinputs(array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
			),
			'user' => array(
				'type' => User::class,
				'optional' => true,
				'query' => function ($query) use ($children, $me) {
					if ($children) {
						$query->where("type", $children, "IN");
					} else {
						$query->where("id", $me->id);
					}
				}
			),
			'title' => array(
				'type' => 'string',
				'optional' => true,
			),
			'timeFrom' => array(
				'type' => 'date',
				'optional' => true,
				'unix' => true
			),
			'timeUntil' => array(
				'type' => 'date',
				'optional' => true,
				'unix' => true
			),
			'activity' => array(
				'type' => 'bool',
				'optional' => true,
			),
			'comparison' => array(
				'type' => 'string',
				'values' => ['equals', 'startswith', 'contains'],
				'default' => 'contains',
				'optional' => true
			)
		));
		$activities = null;
		if (isset($inputs['activity']) and $inputs['activity']) {
			$activities = Log\Activity::getActivityTypes();
		}
		$model = new Log();
		$model->with("user");
		if (isset($inputs['activity']) and $inputs['activity'] and $activities) {
			$model->where("userpanel_logs.type", $activities, "IN");
		}
		if (isset($inputs['id'])) {
			$model->where("userpanel_logs.id", $inputs['id']);
		}
		if (isset($inputs['timeFrom'])) {
			$model->where("userpanel_logs.time", $inputs['timeFrom'], ">=");
		}
		if (isset($inputs['timeUntil'])) {
			$model->where("userpanel_logs.time", $inputs['timeUntil'], "<");
		}
		if (isset($inputs['user'])) {
			$model->where("userpanel_logs.user", $inputs['user']->id);
		}
		if (isset($inputs['title'])) {
			$model->where("userpanel_logs.title", $inputs['title'], $inputs['comparison']);
		}
		if ($children) {
			$model->where("userpanel_users.type", $children, 'IN');
		} else {
			$model->where("userpanel_logs.user", $me->id);
		}
		$model->orderBy('userpanel_logs.id', 'DESC');
		$model->pageLimit = $this->items_per_page;
		$logs = $model->paginate($this->page);
		$view->setDataList($logs);
		$view->setPaginate($this->page, $model->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function view(array $data):response{
		authorization::haveOrFail('logs_view');
		$types = authorization::childrenTypes();
		$user = authentication::getUser();
		$log = new log();
		db::join("userpanel_users", "userpanel_users.id=userpanel_logs.user", "INNER");
		if($types){
			$log->where("userpanel_users.type", $types, 'in');
		}else{
			$log->where("userpanel_users.id", $user->id);
		}
		$log->where("userpanel_logs.id", $data['log']);
		if(!$log = $log->getOne("userpanel_logs.*")){
			throw new NotFound();
		}
		$view = view::byName(views\logs\view::class);
		$view->setUser($user);

		$log->getHandler()->buildFrontend($view);
		$view->setLog($log);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function delete(array $data):response{
		authorization::haveOrFail('logs_delete');
		$types = authorization::childrenTypes();
		$log = new log();
		db::join("userpanel_users", "userpanel_users.id=userpanel_logs.user", "INNER");
		if($types){
			$log->where("userpanel_users.type", $types, 'in');
		}else{
			$log->where("userpanel_users.id", authentication::getID());
		}
		$log->where("userpanel_logs.id", $data['log']);
		if(!$log = $log->getOne("userpanel_logs.*")){
			throw new NotFound();
		}
		$view = view::byName(views\logs\delete::class);
		$view->setLog($log);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function terminate(array $data):response{
		authorization::haveOrFail('logs_delete');
		$types = authorization::childrenTypes();
		$log = new log();
		db::join("userpanel_users", "userpanel_users.id=userpanel_logs.user", "INNER");
		if($types){
			$log->where("userpanel_users.type", $types, 'in');
		}else{
			$log->where("userpanel_users.id", authentication::getID());
		}
		$log->where("userpanel_logs.id", $data['log']);
		if(!$log = $log->getOne("userpanel_logs.*")){
			throw new NotFound();
		}
		$view = view::byName(views\logs\delete::class);
		$view->setLog($log);
		try{
			$this->response->setStatus(false);
			$log->delete();
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url("logs/search"));
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
		}
		$this->response->setView($view);
		return $this->response;
	}
}
