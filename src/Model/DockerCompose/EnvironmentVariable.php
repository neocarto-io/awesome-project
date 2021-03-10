<?php

declare(strict_types=1);

namespace AwesomeProject\Model\DockerCompose;

class EnvironmentVariable
{
    /**
     * @param string $key
     * @param string|null $value
     */
    public function __construct(private string $key, private ?string $value)
    {
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->key}={$this->value}";
    }
}
