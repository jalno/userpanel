<?php

namespace themes\clipone;

use themes\clipone\Navigation\MenuItem;

class Breadcrumb
{
    public static $menu = [];

    public static function addItem(MenuItem $item)
    {
        self::$menu[$item->getName()] = $item;
    }

    public static function prependItem(MenuItem $item)
    {
        $menu = array_reverse(self::$menu, true);
        $menu[$item->getName()] = $item;
        self::$menu = array_reverse($menu, true);
    }

    public static function build()
    {
        $html = '';
        $len = count(self::$menu);
        $x = 0;
        foreach (self::$menu as $item) {
            $icon = $item->getIcon();
            if ($x != $len - 1) {
                $link = $item->getURL();
            } else {
                $link = false;
            }
            $html .= '<li'.($x == $len - 1 ? ' class="active"' : '').'> ';
            if ($icon) {
                $html .= "<i class=\"{$icon}\"></i> ";
            }
            if ($link) {
                $html .= "<a href=\"{$link}\">";
            }
            $html .= $item->getTitle();
            if ($link) {
                $html .= '</a>';
            }
            $html .= '</li>';
            ++$x;
        }

        return $html;
    }

    public static function getByName($name)
    {
        if ('/' == substr($name, -1)) {
            $name = substr($name, 0, strlen($name) - 1);
        }
        $names = explode('/', $name, 2);
        $name = $names[0];
        foreach (self::$menu as $item) {
            if ($item->getName() == $name) {
                if (isset($names[1]) and $names[1]) {
                    return $item->getByName($names[1]);
                } else {
                    return $item;
                }
            }
        }

        return null;
    }
}
