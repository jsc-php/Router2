<?php

namespace JscPhp\Router2;

class Route
{
    private string $regex_pattern;
    private string $route;
    private        $class;
    private        $method;
    private array $parameters;
    private array $parameter_values;
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

    public function getRegexPattern(): string
    {
        return $this->regex_pattern;
    }

    public function setRegexPattern(string $regex_pattern): Route
    {
        $this->regex_pattern = $regex_pattern;
        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): Route
    {
        $this->route = $route;
        return $this;
    }

    public function getClass(): mixed
    {
        return $this->class;
    }

    public function setClass(mixed $class): Route
    {
        $this->class = $class;
        return $this;
    }

    public function getMethod(): mixed
    {
        return $this->method;
    }

    public function setMethod(mixed $method): Route
    {
        $this->method = $method;
        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): Route
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getParameterValues(): array
    {
        return $this->parameter_values;
    }

    public function setParameterValues(array $parameter_values): Route
    {
        $this->parameter_values = $parameter_values;
        return $this;
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