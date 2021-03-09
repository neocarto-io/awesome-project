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
     * @Serializer\Type("array<string,array<string>>")
     */
    private array $routes;

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
}
