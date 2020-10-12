<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Configuration;

use JMS\Serializer\Annotation as Serializer;

class  MainConfiguration
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private string $projectsRoot;

    /**
     * @var array
     * @Serializer\Type("array<string,string>")
     */
    private array $ports;

    /**
     * @var Route[]
     * @Serializer\Type("array<string,array<string>>")
     */
    private array $routes;

    /**
     * @return string
     */
    public function getProjectsRoot(): string
    {
        return $this->projectsRoot;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return array
     */
    public function getPorts(): array
    {
        return $this->ports;
    }

    /**
     * @param string $identifier
     * @return string|null
     */
    public function getPort(string $identifier): ?string
    {
        return $this->ports[$identifier] ?? null;
    }
}
