<?php

namespace AwesomeProject\Manager;

use AwesomeProject\Aggregator\DockerComposeAggregator;
use AwesomeProject\Model\ServiceConfiguration;
use AwesomeProject\Serializer\DockerComposeSerializerHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ServicesManager
{
    private Serializer $serializer;
    private DockerComposeAggregator $dockerComposeAggregator;

    /** @var ServiceConfiguration[] */
    private array $services = [];

    private array $dockerComposeServicesConfigs;
    private array $dockerComposeServicesSlugs;

    public function __construct()
    {
        $this->createSerializer();
        $this->dockerComposeAggregator = new DockerComposeAggregator(getcwd(), $this->serializer);

        foreach ($this->constructServices() as $service) {
            $this->services[$service->getSlug()] = $service;

            if ($service->getDockerComposeConfig()) {
                $this->dockerComposeServicesConfigs[] = $service->getDockerComposeConfig();
                $this->dockerComposeServicesSlugs[] = $service->getSlug();
            }
        }
    }

    /**
     * @return ServiceConfiguration[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return array
     */
    public function getDockerComposeServicesConfigs(): array
    {
        return $this->dockerComposeServicesConfigs;
    }

    /**
     * Merge all docker-compose files from discovered services
     */
    public function compileMainDockerCompose()
    {
        foreach ($this->getServices() as $serviceConfiguration) {
            $this->dockerComposeAggregator->attemptRegistration($serviceConfiguration);
        }

        file_put_contents(
            getcwd() . DIRECTORY_SEPARATOR . 'docker-compose.yaml',
            Yaml::dump($this->serializer->toArray($this->dockerComposeAggregator->getConfiguration()), 4, 4, Yaml::DUMP_NULL_AS_TILDE),
        );
    }

    /**
     * Get services
     * @return \Iterator|ServiceConfiguration[]
     */
    private function constructServices(): \Iterator
    {
        if (!file_exists(getcwd() . DIRECTORY_SEPARATOR . 'awesome-project.json')) {
            throw new \RuntimeException("Not an awesome project :( !");
        }

        $manifest = $this->serializer->deserialize(
            file_get_contents(getcwd() . DIRECTORY_SEPARATOR . 'awesome-project.json'),
            'array',
            'json'
        );

        if (!isset($manifest['servicesRoot'])) {
            throw new \RuntimeException("Services root not configured");
        }

        try {
            $finder = (new Finder())
                ->depth(0)
                ->in($manifest['servicesRoot']);

            foreach ($finder->directories() as $fileInfo) {
                yield new ServiceConfiguration(realpath($fileInfo->getPathname()));
            }
        } catch (DirectoryNotFoundException $exception) {
            throw new \RuntimeException("Services root not valid");
        }
    }

    /**
     * Create serializer instance
     */
    private function createSerializer()
    {
        $this->serializer = SerializerBuilder::create()
            ->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new DockerComposeSerializerHandler());
            })
            ->build();
    }
}
