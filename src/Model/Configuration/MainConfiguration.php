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
     * @var Route[]
     * @Serializer\Type("array<string,AwesomeProject\Model\Configuration\Route>")
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
}
