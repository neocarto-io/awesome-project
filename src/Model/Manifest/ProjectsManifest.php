<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Manifest;

use JMS\Serializer\Annotation as Serializer;

class ProjectsManifest
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private string $root;

    /**
     * @var array|ProjectSource[]
     * @Serializer\Type("array<string,AwesomeProject\Model\Manifest\ProjectSource>")
     */
    private array $sources;

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @param string $projectName
     * @return ProjectSource|null
     */
    public function getSource(string $projectName): ?ProjectSource
    {
        return $this->sources[$projectName] ?? null;
    }

    /**
     * @return array|ProjectSource[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }
}
