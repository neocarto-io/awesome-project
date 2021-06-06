<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Manifest;

use JMS\Serializer\Annotation as Serializer;

class HttpManifest
{
    /**
     * @var array
     * @Serializer\Type("array<string,string>")
     */
    private array $ports;

    /**
     * @var array
     * @Serializer\Type("array")
     */
    private array $routes;

    /**
     * @var array
     * @Serializer\Type("array<string>")
     */
    private array $hostnames = [];

    /**
     * @var array|null
     * @Serializer\Type("array")
     */
    private array $plugins = [];

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getPort(string $portName): ?string
    {
        return $this->ports[$portName] ?? null;
    }

    /**
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * @return array
     */
    public function getHostnames(): array
    {
        return $this->hostnames;
    }
}
