<?php

declare(strict_types=1);

namespace AwesomeProject\Traits;

use AwesomeProject\Manager\DockerComposeManager;

trait DockerComposeAwareTrait
{
    protected DockerComposeManager $dockerComposeManager;

    /**
     * @param DockerComposeManager $dockerComposeManager
     * @return $this
     */
    public function setDockerComposeManager(DockerComposeManager $dockerComposeManager): self
    {
        $this->dockerComposeManager = $dockerComposeManager;
        return $this;
    }
}
