<?php

namespace JscPhp\Router2;
class RouterConfig
{
    private array $paths           = [];
    private int   $fqcn_file_depth = 1000;

    public function __construct()
    {
    }

    public function getFQCNFileDepth(): int
    {
        return $this->fqcn_file_depth;
    }

    public function setFQCNFileDepth(int $fqcn_file_depth): RouterConfig
    {
        $this->fqcn_file_depth = $fqcn_file_depth;
        return $this;
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