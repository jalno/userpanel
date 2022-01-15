<?php
namespace packages\userpanel\controllers;

use packages\base;
use packages\base\{db, db\DuplicateRecord, InputValidation, NotFound, db\Parenthesis, Response};
use packages\userpanel;
use packages\userpanel\{Authentication, AuthorizationException, Authorization, Controller, Date, Log, User, View, Views};

class Logs extends Controller {

	/**
	 * get log from database by check permissions and types
	 *
	 * @param int $logID that is id of given log
	 * @return Log
	 */
	protected static function getLog(int $logID): Log {
		$types = Authorization::childrenTypes();
		$canSearchSystemLogs = Authorization::is_accessed("logs_search_system_logs");
		$log = new Log();
		$log->join(User::class, "user", ($canSearchSystemLogs ? "LEFT" : "INNER"), "userpanel_users.id");
		$parenthesis = new Parenthesis();
		if ($types) {
			$parenthesis->where("userpanel_users.type", $types, "IN");
		} else {
			$parenthesis->where("userpanel_logs.user", Authentication::getID());
		}
		if ($canSearchSystemLogs) {
			$parenthesis->orWhere("userpanel_logs.user", null, "IS");
		}
		$log->where($parenthesis);
		$log->where("userpanel_logs.id", $logID);
		$log = $log->getOne("userpanel_logs.*");
		if (!$log) {
			throw new NotFound();
		}
		return $log;
	}

	/**
	 * @var bool $authentication that indicates user should be authenticate before do anything!
	 */
	protected $authentication = true;
	
	/**
	 * Works with one of logs_search, users_view or profile_view permissions.
	 *
	 * @return Response
	 */
	public function search(): Response {
		$this->response->setStatus(false);
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
		$canSearchSystemLogs = Authorization::is_accessed('logs_search_system_logs');

		$view = View::byName(views\logs\search::class);
		$this->response->setView($view);

		$me = Authentication::getUser();
		$children = ($logs_permission or $users_permission) ? Authorization::childrenTypes() : [];

		$rules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
			),
			'user' => array(
				'type' => User::class,
				'optional' => true,
				'query' => function ($query) use (&$children, &$me) {
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
			'ip' => array(
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
				'optional' => true,
			),
		);
		if ($canSearchSystemLogs) {
			$rules['system_logs'] = array(
				'type' => 'bool',
				'optional' => true,
				'default' => false,
			);
		}
		$inputs = $this->checkInputs($rules);
		$activities = null;
		if (isset($inputs['activity']) and $inputs['activity']) {
			$activities = Log\Activity::getActivityTypes();
		}
		$model = new Log();
		$model->setQueryOption("STRAIGHT_JOIN");
		$model->join(User::class, "user", ($canSearchSystemLogs ? "LEFT" : "INNER"), "id");
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
		if (isset($inputs['system_logs']) and $inputs['system_logs']) {
			$model->where("userpanel_logs.user", null, "IS");
		} elseif (isset($inputs['user'])) {
			$model->where("userpanel_logs.user", $inputs['user']->id);
		}
		if (isset($inputs['title'])) {
			$model->where("userpanel_logs.title", $inputs['title'], $inputs['comparison']);
		}
		if (isset($inputs['ip'])) {
			$model->where("userpanel_logs.ip", $inputs['ip'], $inputs['comparison']);
		}
		$parenthesis = new Parenthesis();
		if ($children) {
			$parenthesis->where("userpanel_users.type", $children, "IN");
		} else {
			$parenthesis->where("userpanel_logs.user", $me->id);
		}
		if ($canSearchSystemLogs) {
			$parenthesis->orWhere("userpanel_logs.user", null, "IS");
		}
		$model->where($parenthesis);

		$logs = $model->cursorPaginate("DESC", $this->items_per_page, array(
			"userpanel_logs.id",
			"userpanel_logs.user",
			"userpanel_logs.ip",
			"userpanel_logs.time",
			"userpanel_logs.title",
			"userpanel_logs.type",
			"userpanel_users.id as userID",
			"userpanel_users.type as userType",
			"userpanel_users.name as userName",
			"userpanel_users.lastname as userLastname",
		));
		foreach ($logs as &$log) {
			if ($log->data["userID"]) {
				$log->user = new User(array(
					"id" => $log->data["userID"],
					"type" => $log->data["userType"],
					"name" => $log->data["userName"],
					"lastname" => $log->data["userLastname"],
				));
			}
		}
		$view->setDataList($logs);
		$view->setCursorPaginate($this->items_per_page, $model->getCursorName(), $model->getNextPageCursor(), $model->getPrevPageCursor());
		$this->response->setStatus(true);
		return $this->response;
	}
	/**
	 * view log based on user permission and types
	 *
	 * @throws NotFound if can not find any log with given id or the user should not access this log
	 * @return Response
	 */
	public function view(array $data): Response {
		$this->response->setStatus(false);
		Authorization::haveOrFail('logs_view');
		$log = self::getLog($data['log']);
		$view = view::byName(views\logs\view::class);
		$this->response->setView($view);
		$view->setLog($log);
		$log->getHandler()->buildFrontend($view);
		$this->response->setStatus(true);
		return $this->response;
	}
	/**
	 * delete log view
	 *
	 * @throws NotFound if can not find any log with given id or the user should not access this log
	 * @return Response
	 */
	public function delete(array $data): Response {
		$this->response->setStatus(false);
		Authorization::haveOrFail('logs_delete');
		$log = self::getLog($data['log']);
		$view = view::byName(views\logs\delete::class);
		$this->response->setView($view);
		$view->setLog($log);
		$this->response->setStatus(true);
		return $this->response;
	}
	/**
	 * remove's log from databse
	 *
	 * @throws NotFound if can not find any log with given id or the user should not access this log
	 * @return Response
	 */
	public function terminate(array $data): Response {
		$this->response->setStatus(false);
		Authorization::haveOrFail('logs_delete');
		$log = self::getLog($data['log']);
		$view = view::byName(views\logs\delete::class);
		$this->response->setView($view);
		$view->setLog($log);
		$log->delete();
		$this->response->setStatus(true);
		$this->response->Go(userpanel\url("logs/search"));
		return $this->response;
	}
}
