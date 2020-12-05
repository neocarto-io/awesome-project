<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Configuration;

use JMS\Serializer\Annotation as Serializer;

class MainConfiguration
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
     * @var array<string,ProjectConfiguration>|null
     * @Serializer\Type("array<string,AwesomeProject\Model\Configuration\ProjectConfiguration>")
     */
    private ?array $projects = null;

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

    /**
     * @return array<string,ProjectConfiguration>|ProjectConfiguration[]
     */
    public function getProjects(): array
    {
        return $this->projects ?? [];
    }

    /**
     * @param string $slug
     * @return ProjectConfiguration|null
     */
    public function getProject(string $slug): ?ProjectConfiguration
    {
        return $this->getProjects()[$slug] ?? null;
    }
}
