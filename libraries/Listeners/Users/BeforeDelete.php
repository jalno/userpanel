<?php

namespace packages\userpanel\Listeners\Users;

use packages\base\{View\Error};
use packages\userpanel\Authorization;
use packages\userpanel\Events as UserpanelEvents;
use packages\userpanel\Log as UserpanelLog;

use function packages\userpanel\Url;

class BeforeDelete
{
    public function check(UserpanelEvents\Users\BeforeDelete $event): void
    {
        $this->checkLogs($event);
    }

    private function checkLogs(UserpanelEvents\Users\BeforeDelete $event): void
    {
        $user = $event->getUser();
        $hasLogs = (new UserpanelLog())->where('user', $user->id)->has();
        if (!$hasLogs) {
            return;
        }

        $message = t('error.packages.userpanel.error.logs.user.delete_user_warn.message');
        $error = new Error('packages.userpanel.error.logs.user.delete_user_warn');
        $error->setType(Error::WARNING);
        if (Authorization::is_accessed('logs_search')) {
            $message .= '<br> '.t('packages.userpanel.error.logs.user.delete_user_warn.view_logs').' ';
            $error->setData([
                [
                    'txt' => '<i class="fa fa-search"></i> '.t('packages.userpanel.error.logs.user.delete_user_warn.view_logs_btn'),
                    'type' => 'btn-warning',
                    'link' => url('logs/search', [
                        'user' => $user->id,
                    ]),
                ],
            ], 'btns');
        } else {
            $message .= '<br> '.t('packages.userpanel.error.logs.user.delete_user_warn.view_logs.tell_someone');
        }
        $error->setMessage($message);

        $event->addError($error);
    }
}
