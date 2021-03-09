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
}
