<?php

declare(strict_types=1);

namespace AwesomeProject\Model\DockerCompose;

use JMS\Serializer\Annotation as Serializer;

class Project
{
    /**
     * @var string|null
     * @Serializer\Exclude()
     */
    private ?string $path = null;
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private string $version;
    /**
     * @var Service[]
     * @Serializer\Type("array<string, AwesomeProject\Model\DockerCompose\Service>")
     */
    private array $services = [];

    /**
     * @var array
     * @Serializer\Type("array")
     */
    private array $networks = [];

    /**
     * @param string $path
     * @param string $version
     */
    public function __construct(string $path = null, $version = '3')
    {
        $this->path = $path;
        $this->version = $version;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return Service[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasService(string $name)
    {
        return isset($this->services[$name]);
    }

    /**
     * @param string $name
     * @return Service|null
     */
    public function getService(string $name): ?Service
    {
        return $this->services[$name] ?? null;
    }

    /**
     * @param string $name
     * @param Service $service
     * @return $this
     */
    public function setService(string $name, Service $service): self
    {
        $this->services[$name] = $service;
        return $this;
    }

    /**
     * @return array
     */
    public function getNetworks(): array
    {
        return $this->networks;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasNetwork(string $name): bool
    {
        return isset($this->networks[$name]);
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function getNetwork(string $name): ?array
    {
        return $this->networks[$name] ?? null;
    }

    public function setNetwork(string $name, ?array $network = null)
    {
        $this->networks[$name] = $network;
        return $this;
    }
}
