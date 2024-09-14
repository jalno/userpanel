<?php

namespace packages\userpanel\Listeners\Users;

use packages\base\Date;
use packages\base\Options;
use packages\base\Translator;
use packages\userpanel\Controllers\Users\Settings as Controller;
use packages\userpanel\Events\Settings as SettingsEvent;

class Settings
{
    public function settingsList(SettingsEvent $settings)
    {
        $tuning = new SettingsEvent\Tuning('userpanel');
        $tuning->setController(Controller::class);
        $tuning->addInput([
            'name' => 'userpanel_timezone',
            'type' => 'string',
            'values' => \DateTimeZone::listIdentifiers(\DateTimeZone::ALL),
        ]);
        $tuning->addInput([
            'name' => 'userpanel_calendar',
            'type' => 'string',
            'values' => [
                'jdate',
                'gregorian',
                'hdate',
            ],
        ]);
        $tuning->addField([
            'name' => 'userpanel_timezone',
            'type' => 'select',
            'label' => t('userpanel.usersettings.message.timezone'),
            'options' => $this->getTimeZonesForSelect(),
        ]);
        $tuning->addField([
            'name' => 'userpanel_calendar',
            'type' => 'select',
            'label' => t('userpanel.usersettings.message.calendar'),
            'options' => [
                [
                    'title' => t('userpanel.usersettings.message.calendar.jdate'),
                    'value' => 'jdate',
                ],
                [
                    'title' => t('userpanel.usersettings.message.calendar.gregorian'),
                    'value' => 'gregorian',
                ],
                [
                    'title' => t('userpanel.usersettings.message.calendar.hdate'),
                    'value' => 'hdate',
                ],
            ],
        ]);
        $userCustoms = $settings->getUser()->option('userpanel_date');
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
        $calendar = $userCustoms['calendar'] ?? Options::get('packages.userpanel.date.calendar');
        $tuning->setDataForm('userpanel_timezone', $timeZone);
        $tuning->setDataForm('userpanel_calendar', $calendar);
        $settings->addTuning($tuning);
    }

    private function getTimeZonesForSelect()
    {
        $timezones = [];
        static $regions = [
            'Asia' => \DateTimeZone::ASIA,
            'Europe' => \DateTimeZone::EUROPE,
            'America' => \DateTimeZone::AMERICA,
            'Africa' => \DateTimeZone::AFRICA,
            'Australia' => \DateTimeZone::AUSTRALIA,
            'Antarctica' => \DateTimeZone::ANTARCTICA,
            'Atlantic' => \DateTimeZone::ATLANTIC,
            'Indian' => \DateTimeZone::INDIAN,
            'Pacific' => \DateTimeZone::PACIFIC,
        ];
        foreach ($regions as $key => $region) {
            foreach (\DateTimeZone::listIdentifiers($region) as $tz) {
                $tzOffset = (new \DateTimeZone($tz))->getOffset(new \DateTime());
                $timezones[t('date.timezone.'.$key)][] = [
                    'title' => t('date.timezone.'.$tz, [
                        'timezone' => ($tzOffset < 0 ? '-' : '+').gmdate('H:i', abs($tzOffset)),
                    ]),
                    'value' => $tz,
                ];
            }
            asort($timezones[t('date.timezone.'.$key)]);
        }

        return $timezones;
    }
}
