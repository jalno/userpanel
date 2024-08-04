<?php

namespace packages\userpanel;

use packages\base\Date as BaseDate;
use packages\base\Options;

class Date extends BaseDate
{
    protected static bool $inited = false;

    public static function setDefaultCalendar(): void
    {
        if ($calendar = Options::get('packages.userpanel.date.calendar')) {
            static::setCanlenderName($calendar);
        } else {
            parent::setDefaultCalendar();
        }
    }

    public static function setUserOptions(): void
    {
        $options = Authentication::getUser()?->option('userpanel_date');
        if (isset($options['timezone'])) {
            static::setTimeZone($options['timezone']);
        }
        if (isset($userOptions['calendar'])) {
            static::setCanlenderName($userOptions['calendar']);
        }
    }

    public static function init(): void
    {
        if (self::$inited) {
            return;
        }
        static::setUserOptions();
        if (!self::$calendar) {
            static::setDefaultCalendar();
        }
        self::$inited = true;
    }
}
