<?php

namespace JscPhp\Router2;

class Router
{
    private RouteArray   $route_array;
    private RouterConfig $router_config;

    public function __construct(RouterConfig $router_config)
    {
        $this->router_config = $router_config;
    }
}