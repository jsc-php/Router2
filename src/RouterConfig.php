<?php

namespace JscPhp\Router2;

class RouterConfig
{

    private string $class_path;
    private array  $search_paths;

    public function __construct()
    {
    }

    public function getClassPath(): string
    {
        return $this->class_path;
    }

    public function setClassPath(string $class_path): RouterConfig
    {
        $this->class_path = $class_path;
        return $this;
    }

    public function getSearchPaths(): array
    {
        return $this->search_paths;
    }

    public function setSearchPaths(array $search_paths): RouterConfig
    {
        $this->search_paths = $search_paths;
        return $this;
    }

    public function addSearchPath(string $search_path): RouterConfig
    {
        $this->search_paths[] = $search_path;
        return $this;
    }

}