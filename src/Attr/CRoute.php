<?php

namespace JscPhp\Router2\Attr;

use JscPhp\Router2\HTTPMethod;

class CRoute
{
    public function __construct(string     $route,
                                HTTPMethod $http_method,
                                int        $priority = 999,
                                bool       $protected = false)
    {
    }
}