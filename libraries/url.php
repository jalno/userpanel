<?php

namespace packages\userpanel;

use packages\base;
use packages\base\Packages;

function url($page = '', $parameters = [], $absolute = false)
{
    $prefix = Packages::package('userpanel')->getOption('urlPrefix');
    if (null === $prefix) {
        $prefix = 'userpanel';
    }
    if ($prefix) {
        $prefix .= '/';
    }

    return base\url($prefix.$page, $parameters, $absolute);
}
