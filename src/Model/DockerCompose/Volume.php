<?php

declare(strict_types=1);

namespace AwesomeProject\Model\DockerCompose;

class Volume
{
    /**
     * @param string $hostPath
     * @param string $containerPath
     */
    public function __construct(private string $hostPath, private string $containerPath)
    {
    }

    /**
     * @return string
     */
    public function getHostPath(): string
    {
        return $this->hostPath;
    }

    /**
     * @return string
     */
    public function getContainerPath(): string
    {
        return $this->containerPath;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->hostPath}:{$this->containerPath}";
    }
}
