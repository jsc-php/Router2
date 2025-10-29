<?php

namespace JscPhp\Router2;

use ReflectionMethod;

class Router
{
    private RouterConfig     $router_config;
    private RouteCollection  $route_collection;
    private Route            $route;
    private ReflectionMethod $reflection_method;

    public function __construct(RouterConfig $router_config)
    {
        $this->router_config = $router_config;
        $this->route_collection = new RouteCollection($router_config);
        if (!empty($paths = $this->router_config->getPaths())) {
            $this->route_collection->processClassPaths($paths);
        }
    }

    public function getReflectionMethod(): ReflectionMethod|false
    {
        return (!empty($this->reflection_method)) ? $this->reflection_method : false;
    }

    public function go()
    {

        if (empty($this->route)) {
            throw new \Exception('Call Router->GetRoute() first');
        }
        $class = $this->route->getClass();
        $method = $this->route->getMethod();
        $class = new $class;
        $arguments = $this->route->getMethodArguments();
        call_user_func_array([$class, $method], $arguments);


    }

    public function getRoute(?string $uri = null, ?string $http_method = null): Route
    {
        if (empty($uri)) {
            $uri = Request::getRequestURI(true);
        }
        if (empty($http_method)) {
            $http_method = Request::getRequestMethod();
        }
        if ($route = $this->route_collection->matchRoute($http_method, $uri)) {
            $this->route = $route;
            $this->reflection_method = new ReflectionMethod($route->getClass(), $route->getMethod());
            return $route;
        }
        throw new \Exception('Route not found');
    }
}