<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Manifest;

use JMS\Serializer\Annotation as Serializer;

class LoggingManifest
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private string $service;
    /**
     * @var array
     * @Serializer\Type("array<string>")
     */
    private array $services = [];
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private string $driver;
    /**
     * @var array
     * @Serializer\Type("array")
     */
    private array $options = [];

    /**
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @param string $serviceId
     * @return bool
     */
    public function hasService(string $serviceId): bool
    {
        return in_array($serviceId, $this->services);
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
