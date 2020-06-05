<?php

declare(strict_types=1);

namespace AwesomeProject\Model\DockerCompose;

class PortMapping
{
    private int $hostPort;
    private int $containerPort;

    /**
     * @param int $hostPort
     * @param int $containerPort
     */
    public function __construct(int $hostPort, int $containerPort)
    {
        $this->hostPort = $hostPort;
        $this->containerPort = $containerPort;
    }

    /**
     * @return int
     */
    public function getHostPort(): int
    {
        return $this->hostPort;
    }

    /**
     * @return int
     */
    public function getContainerPort(): int
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
