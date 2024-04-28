<?php

namespace packages\userpanel;

use packages\base\Options;

class Frontend
{
    private static $logo;
    private static $copyright;

    public static function setLogoHTML($logo)
    {
        self::$logo = $logo;
    }

    public static function getLogoHTML()
    {
        if (!self::$logo) {
            self::$logo = Options::get('packages.userpanel.frontend.logo');
        }

        return self::$logo;
    }

    public static function setCopyRightHTML($copyright)
    {
        self::$copyright = $copyright;
    }

    public static function getCopyRightHTML()
    {
        if (!self::$copyright) {
            self::$copyright = Options::get('packages.userpanel.frontend.copyright');
        }

        return self::$copyright;
    }
}
