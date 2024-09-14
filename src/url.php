<?php

namespace packages\userpanel;

use function packages\base\url as baseUrl;

function url(string $page = '', array $parameters = [], bool $absolute = false): string
{
    $prefix = rtrim(config("userpanel.routes.prefix", "userpanel"), "/") . "/";
    return baseUrl($prefix.$page, $parameters, $absolute);
}
