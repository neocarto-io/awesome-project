<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Manifest;

use JMS\Serializer\Annotation as Serializer;

class MainManifest
{
    /**
     * @var ProjectsManifest
     * @Serializer\Type(ProjectsManifest::class)
     */
    private ProjectsManifest $projects;

    /**
     * @var HttpManifest
     * @Serializer\Type(HttpManifest::class)
     */
    private HttpManifest $http;

    /**
     * @var DockerComposeManifest
     * @Serializer\Type(DockerComposeManifest::class)
     * @Serializer\SerializedName("docker-compose")
     */
    private DockerComposeManifest $dockerCompose;

    /**
     * @var LoggingManifest
     * @Serializer\Type(LoggingManifest::class)
     */
    private LoggingManifest $logging;


    /**
     * @return ProjectsManifest
     */
    public function getProjects(): ProjectsManifest
    {
        return $this->projects;
    }

    /**
     * @return HttpManifest
     */
    public function getHttp(): HttpManifest
    {
        return $this->http;
    }

    /**
     * @return DockerComposeManifest
     */
    public function getDockerCompose(): DockerComposeManifest
    {
        return $this->dockerCompose;
    }

    /**
     * @return LoggingManifest
     */
    public function getLogging(): LoggingManifest
    {
        return $this->logging;
    }
}
