<?php

namespace JscPhp\Router2;

class RouteCollection
{
    private array $routes = [];

    public function __construct()
    {
    }

    public function addRoute(string $method, Route $route, int $priority = 999): void
    {
        $this->routes[strtoupper($method)][$priority][] = $route;
    }

    public function matchRoute(string $http_method, string $uri): Route|false
    {
        /** @var Route $route */
        $http_method = strtoupper($http_method);
        if (!empty($priorities = array_keys($this->routes[$http_method]))) {
            sort($priorities);
            foreach ($priorities as $priority) {
                foreach ($this->routes[$http_method][$priority] as $route) {
                    if ($route->matchURI($uri)) {
                        return $route;
                    }
                }
            }
        };
        return false;
    }
}