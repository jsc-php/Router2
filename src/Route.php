<?php

namespace JscPhp\Router2;

class Route
{
    private string $regex_pattern;
    private string $route;
    private        $class;
    private        $method;
    private array  $parameters;
    private array  $parameter_values;

    private bool $protected = false;

    public function __construct(string $route, $class = '', $method = '', bool $protected = false)
    {
        $this->route = $route;
        $this->class = $class;
        $this->method = $method;
        $this->protected = $protected;
        $this->regex_pattern = $this->_buildRegexPattern($route);
    }

    private function _buildRegexPattern(string $route): string
    {
        if ($route === '/' || strlen($route) === 0) {
            return '/^\/$/';
        }

        $pattern = '/^';
        $route = trim($route, '/');
        $parts = explode('/', $route);
        foreach ($parts as $part) {
            $pattern .= '\/' . $this->_processSegment($part);
        }
        return $pattern . '$/';
    }

    private function _processSegment(string $part): string
    {
        if (!str_starts_with($part, ':')) {
            return $part;
        }
        $part = substr($part, 1);
        $segments = explode('|', $part);
        $this->parameters[] = $segments[0];
        if (count($segments) === 1) {
            return '(\w+)';
        }
        return match ($segments[1]) {
            'w', 'W', 's', 'S' => '(\w+)',
            'd', 'D'           => '(\d+)',
            '#', 'r', 'R'      => '([0-9.]+)',
            default            => $segments[1]
        };
    }

    public function isProtected(): bool
    {
        return $this->protected;
    }

    public function setProtected(bool $protected): Route
    {
        $this->protected = $protected;
        return $this;
    }

    public function matchURI(string $uri): bool
    {
        if (preg_match_all($this->regex_pattern, $uri, $matches) === 1) {
            for ($i = 1; $i < count($matches); $i++) {
                $this->parameter_values[] = $matches[$i][0];
            }
            return true;
        }
        return false;
    }

}