<?php

namespace packages\userpanel\Processes;

use packages\base\DB;
use packages\base\Log;
use packages\base\Process;
use packages\userpanel;
use packages\userpanel\Logs;

class FixEditUserLogs extends Process
{
    public function start()
    {
        Log::setLevel('debug');
        $this->fixTitles();
    }

    public function fixTitles()
    {
        $l = Log::getInstance();
        $l->info('get logs that has not title');

        $type = DB::subQuery();
        $type->where('name', 'userpanel_users_edit');
        $types = $type->get('userpanel_usertypes_permissions', null, 'userpanel_usertypes_permissions.type');

        DB::join('userpanel_users', 'userpanel_users.id=userpanel_logs.user', 'INNER');
        $log = new userpanel\Log();
        $log->where('userpanel_logs.title', '');
        $log->where('userpanel_users.type', $types, 'IN');
        $log->where('userpanel_logs.type', Logs\UserEdit::class);
        $logs = $log->get(null, 'userpanel_logs.*');

        $l->reply(count($logs), ' log found');

        foreach ($logs as $log) {
            $this->fixTitle($log);
        }
    }

    private function fixTitle(userpanel\Log $log)
    {
        $l = Log::getInstance();
        $l->info('handle #', $log->id);
        $l->info('check another logs that happend in ', $log->time);

        $model = new userpanel\Log();
        $model->with('user');
        $model->where('userpanel_logs.type', Logs\UserEdit::class);
        $model->where('userpanel_logs.time', $log->time);
        $model->where('userpanel_logs.id', $log->id, '!=');
        $another = $model->getOne();
        if ($another) {
            $l->reply('another id: #', $another->id);
            $log->title = t('log.userEdit', ['user_name' => $model->user->getFullName(), 'user_id' => $model->user->id]);
            $log->save();
            if (!$another->title) {
                $another->title = t('log.editedYou', ['user_name' => $log->user->getFullName(), 'user_id' => $log->user->id]);
                $another->save();
            }
        } else {
            $l->reply('NotFound');
            $log->title = t('log.profileEdit');
            $log->save();
        }
    }
}
