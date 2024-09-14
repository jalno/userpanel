<?php

namespace packages\userpanel;

use packages\base\Date as BaseDate;
use packages\base\Options;
use packages\base\Translator;

class Date extends BaseDate
{
    public static function format($format, ?int$timestamp = null):string
    {
        self::init();

        return parent::format($format, $timestamp);
    }

    public static function getTimeZone(): string
    {
        self::init();

        return parent::getTimeZone();
    }

    public static function strtotime(string $time, ?int $now = null): int
    {
        self::init();

        return parent::strtotime($time, $now);
    }

    public static function mktime(?int $hour = null, ?int $minute = null, ?int $second = null, ?int $month = null, ?int $day = null, ?int $year = null): int
    {
        self::init();

        return parent::mktime($hour, $minute, $second, $month, $day, $year);
    }

    public static function relativeTime(int $time, string $format = 'short'): string
    {
        self::init();

        return parent::relativeTime($time, $format);
    }

    public static function getFirstDayOfWeek(): int
    {
        self::init();

        return parent::getFirstDayOfWeek();
    }

    public static function getWeekDay(int $day): ?int
    {
        self::init();

        return parent::getWeekDay($day);
    }

    public static function setDefaultcalendar(): void
    {
        $calendar = '';
        $user = Authentication::getUser();
        if ($user) {
            $userOptions = $user->option('userpanel_date');
            if (isset($userOptions['calendar'])) {
                $calendar = $userOptions['calendar'];
            }
        }
        if (!$calendar) {
            $calendar = Translator::getLang()->getCalendar();
        }
        $option = Options::get('packages.userpanel.date');
        if (!$calendar and isset($option['calendar'])) {
            $calendar = $option['calendar'];
        }
        self::setCanlenderName($calendar);
        foreach (Translator::getLangs() as $lang) {
            if ($lang->getCalendar() == $calendar) {
                foreach ($lang->getDateFormats() as $key => $format) {
                    self::setPresetsFormat($key, $format);
                }
                break;
            }
        }
    }

    public static function setDefaultTimeZone()
    {
        $user = Authentication::getUser();
        if ($user) {
            $userOptions = $user->option('userpanel_date');
            if (isset($userOptions['timezone'])) {
                parent::setTimeZone($userOptions['timezone']);

                return;
            }
        }
        $option = Options::get('packages.userpanel.date');
        if (false !== $option and isset($option['timezone'])) {
            parent::setTimeZone($option['timezone']);

            return;
        }
        parent::setDefaultTimeZone();
    }

    public static function init(): void
    {
        if (self::$inited) {
            return;
        }
        self::setDefaultTimeZone();
        if (!self::$calendar) {
            self::setDefaultcalendar();
        }
        self::$inited = true;
    }
}
