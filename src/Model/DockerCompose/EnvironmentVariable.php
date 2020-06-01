<?php

declare(strict_types=1);

namespace AwesomeProject\Model\DockerCompose;

class EnvironmentVariable
{
    private string $key;
    private ?string $value;

    /**
     * @param string $key
     * @param string|null $value
     */
    public function __construct(string $key, ?string $value)
    {
        $this->key = $key;
        $this->value = $value;
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
