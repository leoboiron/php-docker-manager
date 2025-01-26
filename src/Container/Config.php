<?php

namespace PhpDockerManager\Container;

class Config
{
    private string $image;
    private string $tag;
    private ?string $name = null;
    private array $labels = [];
    private array $envVars = [];
    private ?string $network = null;

    public function __construct(string $image, string $tag)
    {
        $this->image = $image;
        $this->tag = $tag;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function addLabel(string $key, string $value): self
    {
        $this->labels[$key] = $value;

        return $this;
    }

    public function getEnvVars(): array
    {
        return $this->envVars;
    }

    public function addEnvVar(string $key, string $value): self
    {
        $this->envVars[$key] = $value;

        return $this;
    }

    public function getNetwork(): ?string
    {
        return $this->network;
    }

    public function setNetwork(?string $network): self
    {
        $this->network = $network;

        return $this;
    }
}
