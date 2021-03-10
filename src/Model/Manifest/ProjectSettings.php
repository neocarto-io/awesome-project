<?php

declare(strict_types=1);

namespace AwesomeProject\Model\Manifest;

class ProjectSettings
{
    private ProjectSource $source;

    /**
     * @return ProjectSource
     */
    public function getSource(): ProjectSource
    {
        return $this->source;
    }
}
