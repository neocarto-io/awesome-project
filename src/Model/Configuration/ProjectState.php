<?php

namespace AwesomeProject\Model\Configuration;

class ProjectState
{
    public const DOCKER_COMPOSE = 'docker-compose';
    public const PHP_COMPOSER = 'composer';

    private string $slug;
    private string $path;
    private array $configurations;

    public function __construct(string $path)
    {
        $this->slug = basename($path);
        $this->path = $path;

        $this->configurations = [
            self::DOCKER_COMPOSE => file_exists($configPath = $path . DIRECTORY_SEPARATOR . 'docker-compose.yaml') ? $configPath : null,
            self::PHP_COMPOSER => file_exists($configPath = $path . DIRECTORY_SEPARATOR . 'composer.json') ? $configPath : null
        ];
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
     * @param string $configId
     * @return bool
     */
    public function hasConfiguration(string $configId): bool
    {
        return isset($this->configurations[$configId]) && !is_null($this->configurations[$configId]);
    }

    /**
     * @param string $configId
     * @param $configuration
     * @return $this
     */
    public function setConfiguration(string $configId, $configuration): self
    {
        $this->configurations[$configId] = $configuration;
        return $this;
    }

    /**
     * @param string $configId
     * @return mixed
     */
    public function getConfiguration(string $configId)
    {
        return $this->configurations[$configId] ?? null;
    }
}
