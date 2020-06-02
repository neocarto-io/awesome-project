<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Configuration;

use JMS\Serializer\Annotation as Serializer;

class Route
{
    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     */
    private array $hosts;
    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     */
    private array $paths;
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private string $target;

    /**
     * @return string[]
     */
    public function getHosts(): array
    {
        return $this->hosts;
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }
}
