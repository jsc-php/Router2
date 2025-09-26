<?php

namespace JscPhp\Router2;

class Router
{
    private RouterConfig    $router_config;
    private RouteCollection $route_collection;
    private Route           $route;

    public function __construct(RouterConfig $router_config)
    {
        $this->router_config = $router_config;
        $this->route_collection = new RouteCollection();
        if (!empty($paths = $this->router_config->getPaths())) {
            $this->route_collection->processClassPaths($paths, $this->router_config->getFQCNFileDepth());
        }
    }

    /**
     * Retrieves the Fully Qualified Class Name (FQCN) from a PHP file by parsing its contents.
     *
     * @param string $filepath The absolute or relative path to the PHP file to parse.
     *
     * @return string|false The FQCN of the class if found, otherwise false.
     */
    public static function getFQCNFromFile(string $filepath, int $length = 1000): string|false
    {
        $namespace = null;
        $class = null;
        $contents = file_get_contents($filepath, length: $length);
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

    public function getRoute(?string $uri = null, ?string $http_method = null): Route|null
    {
        if (empty($uri)) {
            $uri = Request::getRequestURI(true);
        }
        if (empty($http_method)) {
            $http_method = Request::getRequestMethod();
        }
        if ($route = $this->route_collection->matchRoute($http_method, $uri)) {
            $this->route = $route;
            return $route;
        }
        return null;
    }

    public function go()
    {

    }
}