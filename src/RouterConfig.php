<?php

namespace JscPhp\Router2;
class RouterConfig
{
    private array $paths = [];

    public function __construct()
    {
    }

    public function addPath(string $path): void
    {
        $this->paths[] = $path;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }


}