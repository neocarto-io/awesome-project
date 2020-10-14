<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Configuration;

use JMS\Serializer\Annotation as Serializer;

class ProjectConfiguration
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    private string $source;

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return $this
     */
    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }
}
