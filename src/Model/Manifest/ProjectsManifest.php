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
     * @var array|ProjectSettings[]
     * @Serializer\Type("array<string,AwesomeProject\Model\Manifest\ProjectSettings>")
     */
    private array $settings;

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @param string $projectName
     * @return ProjectSettings|null
     */
    public function getProjectSettings(string $projectName): ?ProjectSettings
    {
        return $this->settings[$projectName] ?? null;
    }

    /**
     * @return array|ProjectSettings[]
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
}
