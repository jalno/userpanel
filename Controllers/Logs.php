<?php

namespace packages\userpanel\Controllers;

use packages\base\DB\Parenthesis;
use packages\base\NotFound;
use packages\base\Response;
use packages\userpanel;
use packages\userpanel\Authentication;
use packages\userpanel\Authorization;
use packages\userpanel\AuthorizationException;
use packages\userpanel\Controller;
use packages\userpanel\Log;
use packages\userpanel\User;
use packages\userpanel\View;
use packages\userpanel\Views;

class Logs extends Controller
{
    /**
     * get log from database by check permissions and types.
     *
     * @param int $logID that is id of given log
     */
    protected static function getLog(int $logID): Log
    {
        $types = Authorization::childrenTypes();
        $canSearchSystemLogs = Authorization::is_accessed('logs_search_system_logs');
        $log = new Log();
        $log->join(User::class, 'user', $canSearchSystemLogs ? 'LEFT' : 'INNER', 'userpanel_users.id');
        $parenthesis = new Parenthesis();
        if ($types) {
            $parenthesis->where('userpanel_users.type', $types, 'IN');
        } else {
            $parenthesis->where('userpanel_logs.user', Authentication::getID());
        }
        if ($canSearchSystemLogs) {
            $parenthesis->orWhere('userpanel_logs.user', null, 'IS');
        }
        $log->where($parenthesis);
        $log->where('userpanel_logs.id', $logID);
        $log = $log->getOne('userpanel_logs.*');
        if (!$log) {
            throw new NotFound();
        }

        return $log;
    }

    /**
     * @var bool that indicates user should be authenticate before do anything!
     */
    protected $authentication = true;

    /**
     * Works with one of logs_search, users_view or profile_view permissions.
     */
    public function search(): Response
    {
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
            throw new AuthorizationException('userpanel_logs_search');
        }
        $canSearchSystemLogs = Authorization::is_accessed('logs_search_system_logs');

        $view = View::byName(Views\Logs\Search::class);
        $this->response->setView($view);

        $me = Authentication::getUser();
        $children = ($logs_permission or $users_permission) ? Authorization::childrenTypes() : [];

        $rules = [
            'id' => [
                'type' => 'number',
                'optional' => true,
            ],
            'user' => [
                'type' => User::class,
                'optional' => true,
                'query' => function ($query) use (&$children, &$me) {
                    if ($children) {
                        $query->where('type', $children, 'IN');
                    } else {
                        $query->where('id', $me->id);
                    }
                },
            ],

            'title' => [
                'type' => 'string',
                'optional' => true,
            ],
            'ip' => [
                'type' => 'string',
                'optional' => true,
            ],
            'timeFrom' => [
                'type' => 'date',
                'optional' => true,
                'unix' => true,
            ],
            'timeUntil' => [
                'type' => 'date',
                'optional' => true,
                'unix' => true,
            ],
            'activity' => [
                'type' => 'bool',
                'optional' => true,
            ],
            'comparison' => [
                'type' => 'string',
                'values' => ['equals', 'startswith', 'contains'],
                'default' => 'contains',
                'optional' => true,
            ],
        ];
        if ($canSearchSystemLogs) {
            $rules['system_logs'] = [
                'type' => 'bool',
                'optional' => true,
                'default' => false,
            ];
        }
        $inputs = $this->checkInputs($rules);
        $activities = null;
        if (isset($inputs['activity']) and $inputs['activity']) {
            $activities = Log\Activity::getActivityTypes();
        }
        $model = new Log();
        $model->setQueryOption('STRAIGHT_JOIN');
        $model->join(User::class, 'user', $canSearchSystemLogs ? 'LEFT' : 'INNER', 'id');
        if (isset($inputs['activity']) and $inputs['activity'] and $activities) {
            $model->where('userpanel_logs.type', $activities, 'IN');
        }
        if (isset($inputs['id'])) {
            $model->where('userpanel_logs.id', $inputs['id']);
        }
        if (isset($inputs['timeFrom'])) {
            $model->where('userpanel_logs.time', $inputs['timeFrom'], '>=');
        }
        if (isset($inputs['timeUntil'])) {
            $model->where('userpanel_logs.time', $inputs['timeUntil'], '<');
        }
        if (isset($inputs['system_logs']) and $inputs['system_logs']) {
            $model->where('userpanel_logs.user', null, 'IS');
        } elseif (isset($inputs['user'])) {
            $model->where('userpanel_logs.user', $inputs['user']->id);
        }
        if (isset($inputs['title'])) {
            $model->where('userpanel_logs.title', $inputs['title'], $inputs['comparison']);
        }
        if (isset($inputs['ip'])) {
            $model->where('userpanel_logs.ip', $inputs['ip'], $inputs['comparison']);
        }
        $parenthesis = new Parenthesis();
        if ($children) {
            $parenthesis->where('userpanel_users.type', $children, 'IN');
        } else {
            $parenthesis->where('userpanel_logs.user', $me->id);
        }
        if ($canSearchSystemLogs) {
            $parenthesis->orWhere('userpanel_logs.user', null, 'IS');
        }
        $model->where($parenthesis);

        $logs = $model->cursorPaginate('DESC', $this->items_per_page, [
            'userpanel_logs.id',
            'userpanel_logs.user',
            'userpanel_logs.ip',
            'userpanel_logs.time',
            'userpanel_logs.title',
            'userpanel_logs.type',
            'userpanel_users.id as userID',
            'userpanel_users.type as userType',
            'userpanel_users.name as userName',
            'userpanel_users.lastname as userLastname',
        ]);
        foreach ($logs as &$log) {
            if ($log->data['userID']) {
                $log->user = new User([
                    'id' => $log->data['userID'],
                    'type' => $log->data['userType'],
                    'name' => $log->data['userName'],
                    'lastname' => $log->data['userLastname'],
                ]);
            }
        }
        $view->setDataList($logs);
        $view->setCursorPaginate($this->items_per_page, $model->getCursorName(), $model->getNextPageCursor(), $model->getPrevPageCursor());
        $this->response->setStatus(true);

        return $this->response;
    }

    /**
     * view log based on user permission and types.
     *
     * @throws NotFound if can not find any log with given id or the user should not access this log
     */
    public function view(array $data): Response
    {
        $this->response->setStatus(false);
        Authorization::haveOrFail('logs_view');
        $log = self::getLog($data['log']);
        $view = View::byName(Views\Logs\View::class);
        $this->response->setView($view);
        $view->setLog($log);
        $log->getHandler()->buildFrontend($view);
        $this->response->setStatus(true);

        return $this->response;
    }

    /**
     * delete log view.
     *
     * @throws NotFound if can not find any log with given id or the user should not access this log
     */
    public function delete(array $data): Response
    {
        $this->response->setStatus(false);
        Authorization::haveOrFail('logs_delete');
        $log = self::getLog($data['log']);
        $view = View::byName(Views\Logs\Delete::class);
        $this->response->setView($view);
        $view->setLog($log);
        $this->response->setStatus(true);

        return $this->response;
    }

    /**
     * remove's log from databse.
     *
     * @throws NotFound if can not find any log with given id or the user should not access this log
     */
    public function terminate(array $data): Response
    {
        $this->response->setStatus(false);
        Authorization::haveOrFail('logs_delete');
        $log = self::getLog($data['log']);
        $view = View::byName(Views\Logs\Delete::class);
        $this->response->setView($view);
        $view->setLog($log);
        $log->delete();
        $this->response->setStatus(true);
        $this->response->Go(userpanel\Url('logs/search'));

        return $this->response;
    }
}
