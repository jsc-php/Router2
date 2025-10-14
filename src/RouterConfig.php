<?php

namespace JscPhp\Router2;
class RouterConfig
{
    private array  $paths            = [];
    private bool   $protect_default  = false;
    private string $protect_redirect = '';
    private bool   $dev              = false;

    public function __construct()
    {
    }

    public function getProtectRedirect(): string
    {
        return $this->protect_redirect;
    }

    public function setProtectRedirect(string $protect_redirect): RouterConfig
    {
        $this->protect_redirect = $protect_redirect;
        return $this;
    }

    public function isProtectDefault(): bool
    {
        return $this->protect_default;
    }

    public function setProtectDefault(bool $protect_default): RouterConfig
    {
        $this->protect_default = $protect_default;
        return $this;
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


    public function addPath(string $class_path, string $sub_path = ''): void
    {
        $this->paths[] = [$class_path, $sub_path];
    }

    public function getPaths(): array
    {
        return $this->paths;
    }


}