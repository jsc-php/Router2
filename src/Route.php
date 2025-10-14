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

    public function __construct(string $route, $class = '', $method = '')
    {
        $this->route = $route;
        $this->class = $class;
        $this->method = $method;
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
            return '([^\/]+)';
        }
        $match = strtolower($segments[1]);
        return match ($match) {
            's', 'string'       => '([^\/]+)',
            'w', 'word'         => '([\w]+)',
            'd', 'i', 'integer' => '(\d+)',
            '#', 'r'            => '([0-9.]+)',
            '$'                 => '([0-9.$]+)',
            default             => '(' . $segments[1] . ')'
        };
    }


    public function getRegexPattern(): string
    {
        return $this->regex_pattern;
    }


    public function getRoute(): string
    {
        return $this->route;
    }


    public function getClass(): mixed
    {
        return $this->class;
    }


    public function getMethod(): mixed
    {
        return $this->method;
    }


    public function getParameters(): array
    {
        return $this->parameters;
    }


    public function getParameterValues(): array
    {
        return $this->parameter_values;
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

    public function getMethodArguments(): array
    {
        $ret = array();
        if (!empty($this->parameters)) {
            for ($i = 0; $i < count($this->parameters); $i++) {
                $ret[$this->parameters[$i]] = $this->parameter_values[$i];
            }
        }

        return $ret;
    }

}