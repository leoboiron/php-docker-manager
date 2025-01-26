<?php

namespace PhpDockerManager\Container;

use PhpDockerManager\Container\Config;

class Instance
{
    private Config $config;
    private string $id;

    public function __construct(Config $config, string $id)
    {
        $this->config = $config;
        $this->id = $id;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
