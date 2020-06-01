<?php

namespace AwesomeProject\Model;

class ServiceConfiguration
{
    private string $slug;
    private string $path;
    private ?string $dockerComposeConfig;
    private ?string $phpComposerConfig;

    public function __construct(string $path)
    {
        $this->slug = basename($path);
        $this->path = $path;
        $this->dockerComposeConfig = file_exists($configPath = $path . DIRECTORY_SEPARATOR . 'docker-compose.yaml') ? $configPath : null;
        $this->phpComposerConfig = file_exists($configPath = $path . DIRECTORY_SEPARATOR . 'composer.json') ? $configPath : null;

    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getDockerComposeConfig(): ?string
    {
        return $this->dockerComposeConfig;
    }

    /**
     * @return string|null
     */
    public function getPhpComposerConfig(): ?string
    {
        return $this->phpComposerConfig;
    }
}
