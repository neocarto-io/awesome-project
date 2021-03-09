<?php

declare(strict_types=1);

namespace AwesomeProject\Model;

use AwesomeProject\Model\Manifest\ProjectSource;

class Project
{
    public function __construct(private string $name, private string $path, private ?ProjectSource $source)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getSource(): ?ProjectSource
    {
        return $this->source;
    }

    public function isGit(): bool
    {
        return file_exists("{$this->path}/.git");
    }

    public function isPhpComposer(): bool
    {
        return file_exists("{$this->path}/composer.json");
    }

    public function isDockerCompose(): bool
    {
        return file_exists($this->getDockerComposePath());
    }

    public function getDockerComposePath(): string
    {
        return "{$this->path}/docker-compose.yaml";
    }
}
