<?php

namespace packages\userpanel\Processes;

use packages\base\Log;
use packages\base\Process;
use packages\base\Response;
use packages\userpanel\Log as UserLog;
use packages\userpanel\Logs\Register;
use packages\userpanel\User;

class FixUserRegisteredAt extends Process
{
    public function run(): Response
    {
        Log::setLevel('debug');
        $log = Log::getInstance();
        $log->info('get register logs');
        $logs = new UserLog();
        $logs->where('type', Register::class);
        $logs = $logs->get();
        $log->reply(count($logs), ' logs found');

        foreach ($logs as $item) {
            $parameters = $item->parameters;
            $user = null;
            if (isset($parameters['inputs']['email'])) {
                $log->info("try to find user by email: '{$parameters['inputs']['email']}'");
                $user = (new User())->where('email', $parameters['inputs']['email'])->getOne();
                if ($user) {
                    $log->reply('found, #', $user->id);
                } else {
                    $log->reply('notfound');
                }
            }
            if (!$user and isset($parameters['inputs']['cellphone'])) {
                $log->info("try to find user by cellphone: '{$parameters['inputs']['cellphone']}'");
                $user = (new User())->where('cellphone', $parameters['inputs']['cellphone'])->getOne();
                if ($user) {
                    $log->reply('found, #', $user->id);
                } else {
                    $log->reply('notfound');
                }
            }
            if (!$user) {
                $log->info('skip this user in Log: #', $item->id);
                continue;
            }
            $user->registered_at = $item->time;
            $user->save();
        }

        return new Response(true);
    }
}
