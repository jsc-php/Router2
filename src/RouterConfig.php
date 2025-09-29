<?php

namespace JscPhp\Router2;
class RouterConfig
{
    private array $paths           = [];
    private int   $fqcn_file_depth = 1000;

    private bool $dev = false;

    public function __construct()
    {
    }

    public function isDev(): bool
    {
        return $this->dev;
    }

    public function setDev(bool $dev): RouterConfig
    {
        $this->dev = $dev;
        return $this;
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

    public function addPath(string $class_path, string $sub_path = ''): void
    {
        $this->paths[] = [$class_path, $sub_path];
    }

    public function getPaths(): array
    {
        return $this->paths;
    }


}