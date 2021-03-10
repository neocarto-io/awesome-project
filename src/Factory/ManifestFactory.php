<?php

declare(strict_types=1);

namespace AwesomeProject\Factory;

use AwesomeProject\Model\Manifest\MainManifest;
use JMS\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

class ManifestFactory
{
    /**
     * @param Serializer $serializer
     * @param string $manifestFilename
     */
    public function __construct(
        private Serializer $serializer,
        private string $manifestFilename = 'awesome-project.yaml'
    ) {
    }

    /**
     * @return MainManifest
     */
    public function getMainManifest(): MainManifest
    {
        if (!file_exists($this->manifestFilename)) {
            throw new \RuntimeException("Main manifest {$this->manifestFilename} is missing");
        }

        return $this->serializer->fromArray(Yaml::parseFile($this->manifestFilename), MainManifest::class);
    }
}
