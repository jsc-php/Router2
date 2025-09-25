<?php

namespace JscPhp\Router2;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Router
{
    private RouterConfig    $router_config;
    private RouteCollection $route_collection;

    public function __construct(RouterConfig $router_config)
    {
        $this->router_config = $router_config;
        $this->route_collection = new RouteCollection();
        $this->_processClassPaths();
    }

    private function _processClassPaths(): void
    {
        foreach ($this->router_config->getPaths() as $path) {
            if (is_dir($path)) {
                $di = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($di);
                foreach ($files as $file) {
                    $file_path = $file->getPathname();
                    include_once $file_path;
                    if ($class = self::getFQCNFromFile($file_path)) {
                        $reflect = new \ReflectionClass($class);
                        $c_attributes = $reflect->getAttributes(CRoute::class);
                        if (!empty($c_attributes)) {
                            $methods = $reflect->getMethods(\ReflectionMethod::IS_PUBLIC);
                            foreach ($methods as $method) {
                                $attributes = $method->getAttributes(MRoute::class);
                                foreach ($attributes as $attribute) {
                                    $args = $attribute->getArguments();
                                    $route = $args['route'] ?? $args[0];
                                    $http_method = strtoupper($args['method'] ?? $args[1] ?? 'get');
                                    $priority = $args['priority'] ?? $args[2] ?? 999;
                                    $protected = $args['protected'] ?? $args[3] ?? true;
                                    $route = new Route($route, $class, $method->getName(), $protected);
                                    $this->route_collection->addRoute($http_method, $route, $priority);
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
    public static function getFQCNFromFile(string $filepath): string|false
    {
        $namespace = null;
        $class = null;
        $contents = file_get_contents($filepath);
        $tokens = token_get_all($contents);
        $i = 0;
        while ($i < count($tokens)) {
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

    public function route(?string $uri = null, ?string $http_method = null): void
    {
        if (empty($uri)) {
            $uri = Request::getRequestURI(true);
        }
        if (empty($http_method)) {
            $http_method = Request::getRequestMethod();
        }
        $route = $this->route_collection->matchRoute($http_method, $uri);
        var_dump($route);
    }
}