<?php

declare(strict_types=1);

namespace AwesomeProject\Model\DockerCompose;

class PortMapping
{
    private string $hostPort;
    private string $containerPort;

    /**
     * @param string $hostPort
     * @param string $containerPort
     */
    public function __construct(string $hostPort, string $containerPort)
    {
        $this->hostPort = $hostPort;
        $this->containerPort = $containerPort;
    }

    /**
     * @return string
     */
    public function getHostPort(): string
    {
        return $this->hostPort;
    }

    /**
     * @return string
     */
    public function getContainerPort(): string
    {
        return $this->containerPort;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->hostPort}:{$this->containerPort}";
    }
}
