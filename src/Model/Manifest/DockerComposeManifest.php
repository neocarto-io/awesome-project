<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Manifest;

use AwesomeProject\Model\DockerCompose\Service;
use JMS\Serializer\Annotation as Serializer;

class DockerComposeManifest
{
    /**
     * @var array|Service[]
     * @Serializer\Type("array<string,AwesomeProject\Model\DockerCompose\Service>")
     */
    private array $global;

    /**
     * @param string $serviceName
     * @return Service|null
     */
    public function getGlobalService(string $serviceName): ?Service
    {
        return $this->global[$serviceName] ?? null;
    }
}
