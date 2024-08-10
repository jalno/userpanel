<?php

namespace packages\userpanel\Controllers\Users;

use packages\base\Date;
use packages\base\Options;
use packages\base\Translator;
use packages\userpanel\Events\Settings\Controller;
use packages\userpanel\Events\Settings\Log;
use packages\userpanel\User;
use themes\clipone\Views;


class Settings implements Controller
{
    public function store(array $inputs, User $user): array
    {
        $logs = [];
        $changed = false;
        $userCustoms = $user->option('userpanel_date');
        if (isset($inputs['userpanel_calendar'])) {
            $calendar = $userCustoms['calendar'] ?? Options::get('packages.userpanel.date.calendar');
            if ($inputs['userpanel_calendar'] != $calendar) {
                $userCustoms['calendar'] = $inputs['userpanel_calendar'];
                $logs[] = new Log('userpanel_calendar', $calendar, $inputs['userpanel_calendar'], t('userpanel.usersettings.message.calendar'));
                $changed = true;
            }
        }
        if (isset($inputs['userpanel_timezone'])) {
            $timeZone = '';
            if (isset($userCustoms['timezone'])) {
                $timeZone = $userCustoms['timezone'];
            }
            $option = Options::get('packages.userpanel.date');
            if (!$timeZone and false !== $option and isset($option['timezone'])) {
                $timeZone = $option['timezone'];
            }
            if (!$timeZone) {
                $timeZone = Date::getTimeZone();
            }
            if ($timeZone != $inputs['userpanel_timezone']) {
                $userCustoms['timezone'] = $inputs['userpanel_timezone'];
                $logs[] = new Log('userpanel_timezone', $timeZone, $inputs['userpanel_timezone'], t('userpanel.usersettings.message.timezone'));
                $changed = true;
            }
        }
        if ($changed) {
            $user->setOption('userpanel_date', $userCustoms);
        }

        return $logs;
    }
}
