<?php

namespace JscPhp\Router2;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class MRoute
{
    public function __construct(string $route,
                                string $http_method = 'get',
                                int    $priority = 999,
                                ?bool  $protected = null)
    {
    }
}