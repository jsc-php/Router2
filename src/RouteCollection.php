<?php

namespace JscPhp\Router2;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RouteCollection
{
    private array $routes = [];

    public function __construct()
    {
    }

    public function matchRoute(string $http_method, string $uri): Route|false
    {
        /** @var Route $route */
        $http_method = strtoupper($http_method);
        if (isset($this->routes[$http_method])) {
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
        }
        return false;
    }

    public function processClassPaths(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $di = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($di);
                foreach ($files as $file) {
                    $file_path = $file->getPathname();
                    if ($class = Router::getFQCNFromFile($file_path)) {
                        $reflect = new \ReflectionClass($class);
                        $c_attributes = $reflect->getAttributes(CRoute::class);
                        if (!empty($c_attributes)) {
                            $methods = $reflect->getMethods(\ReflectionMethod::IS_PUBLIC);
                            foreach ($methods as $method) {
                                $attributes = $method->getAttributes(MRoute::class);
                                if (!empty($attributes)) {
                                    foreach ($attributes as $attribute) {
                                        $args = $attribute->getArguments();
                                        $route = $args['route'] ?? $args[0] ?? null;
                                        if ($route) {
                                            $http_method = strtoupper($args['method'] ?? $args[1] ?? 'get');
                                            $priority = $args['priority'] ?? $args[2] ?? 999;
                                            $protected = $args['protected'] ?? $args[3] ?? true;
                                            $route = new Route($route, $class, $method->getName(), $protected);
                                            $this->addRoute($http_method, $route, $priority);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function addRoute(string $method, Route $route, int $priority = 999): void
    {
        $this->routes[strtoupper($method)][$priority][] = $route;
    }
}