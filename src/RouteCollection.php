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
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $di = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($di);
                foreach ($files as $file) {
                    $file_path = $file->getPathname();
                    if ($class = $this->getFQCNFromFile($file_path)) {
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

    /**
     * Retrieves the Fully Qualified Class Name (FQCN) from a PHP file by parsing its contents.
     *
     * @param string $filepath The absolute or relative path to the PHP file to parse.
     *
     * @return string|false The FQCN of the class if found, otherwise false.
     */
    public function getFQCNFromFile(string $filepath): string|false
    {
        $namespace = null;
        $class = null;
        $contents = file_get_contents($filepath, length: $this->router_config->getFQCNFileDepth());
        $tokens = token_get_all($contents);
        $i = 0;
        while ($i < count($tokens) && $i < 50) {
            if (is_array($tokens[$i]) && $tokens[$i][0] === T_NAMESPACE) {
                // Find the namespace declaration
                $i++; //Move past the T_NAMESPACE
                while (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
                    $i++; // Skip Whitespace
                }
                $namespace = $tokens[$i][1];

            } elseif (is_array($tokens[$i]) && $tokens[$i][0] === T_CLASS) {
                // Find the class declaration
                $i++; // Move past T_CLASS
                while (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
                    $i++; // Skip whitespace
                }
                $class = $tokens[$i][1];

            }
            $i++;
            if (isset($namespace, $class)) {
                return "\\$namespace\\$class";
            }
        }
        return false;
    }

    public function addRoute(string $method, Route $route, int $priority = 999): void
    {
        $this->routes[strtoupper($method)][$priority][] = $route;
    }
}