<?php

declare(strict_types=1);

namespace AwesomeProject\Model\DockerCompose;

class BuildConfiguration
{
    private ?string $context = null;
    private ?string $dockerfile = null;
    private ?array $args = null;

    /**
     * @return string|null
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * @param string|null $context
     * @return $this
     */
    public function setContext(?string $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDockerfile(): ?string
    {
        return $this->dockerfile;
    }

    /**
     * @param string|null $dockerfile
     * @return $this
     */
    public function setDockerfile(?string $dockerfile): self
    {
        $this->dockerfile = $dockerfile;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getArgs(): ?array
    {
        return $this->args;
    }

    /**
     * @param array|null $args
     * @return $this
     */
    public function setArgs(?array $args): self
    {
        $this->args = $args;
        return $this;
    }
}
