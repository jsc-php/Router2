<?php

namespace JscPhp\Router2;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RouteCollection
{
    private array        $routes = [];
    private RouterConfig $router_config;

    public function __construct(RouterConfig $router_config)
    {
        $this->router_config = $router_config;
    }

    /**
     * Matches the given HTTP method and URI to a route.
     *
     * @param string $http_method The HTTP method (e.g., GET, POST) used for the request.
     * @param string $uri         The URI of the request to match against defined routes.
     *
     * @return Route|false Returns the matched Route object if found, or false if no route is matched.
     */
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

    /**
     * @param array $paths
     *
     * @return void
     * @throws \ReflectionException
     */
    public function processClassPaths(array $paths): void
    {
        $namespace = '';
        foreach ($paths as $path) {
            [$class_path, $sub_path] = $path;
            $class_path = trim($class_path, '/');
            $sub_path = trim($sub_path, '/');
            $path = "/$class_path";
            if (!empty($sub_path)) {
                $path .= "/$sub_path";
            }
            if (is_dir($path)) {
                $di = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($di);
                foreach ($files as $file) {
                    $file_path = $file->getPathname();
                    if ($this->router_config->isDev()) {
                        include_once $file_path;
                    }
                    $class = str_replace($class_path, '', $file_path);
                    $class = str_replace(DIRECTORY_SEPARATOR, '\\', $class);
                    $class = str_replace('.php', '', $class);
                    $class = trim($class, '\\');
                    $class = "\\$class";

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
                                        $protected = $args['protected'] ?? $args[3] ?? $this->router_config->isProtectDefault();
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

    public
    function addRoute(string $method, Route $route, int $priority = 999): void
    {
        $this->routes[strtoupper($method)][$priority][] = $route;
    }

    /**
     * Retrieves the fully qualified class name (FQCN) of a class annotated with #[CRoute]
     * from the given file path, including its namespace.
     *
     * @param string $file_path The path to the file to be analyzed.
     *
     * @return string|false Returns the fully qualified class name (namespace + class)
     *                      if found and annotated with #[CRoute], or false if no such
     *                      class or annotation is found or the file is unreadable.
     */
    public function getCRouteNamespace(string $file_path): string|false
    {
        try {
            $namespace = null;
            $class = null;
            $c_route = false;
            if (is_readable($file_path)) {
                $handle = fopen($file_path, 'r');
                $i = 0;
                while (($line = fgets($handle)) && ($i < 50)) {
                    if (str_starts_with($line, 'namespace')) {
                        preg_match('/^namespace\s+([^\s]+)/', $line, $matches);
                        $namespace = trim($matches[1], ';');
                        break;
                    }
                    $i++;
                }
                //var_dump($namespace, $class, $c_route);
                fclose($handle);
                return "\\$namespace";
            }
            return false;
        } catch (\Exception $ex) {

        }

    }

    /**
     * @param string $class
     *
     * @return void
     * @throws \ReflectionException
     */
    public
    function processClass(string $class): void
    {
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