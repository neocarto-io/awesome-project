<?php

namespace AwesomeProject\Manager;

use AwesomeProject\Aggregator\DockerComposeAggregator;
use AwesomeProject\Model\Configuration\MainConfiguration;
use AwesomeProject\Model\ServiceConfiguration;
use AwesomeProject\Serializer\DockerComposeSerializerHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ServicesManager
{
    private Serializer $serializer;
    private DockerComposeAggregator $dockerComposeAggregator;

    private MainConfiguration $mainConfiguration;

    /** @var ServiceConfiguration[] */
    private array $services = [];


    public function __construct()
    {
        $this->createSerializer();
        $this->dockerComposeAggregator = new DockerComposeAggregator(getcwd(), $this->serializer);

        foreach ($this->constructServices() as $service) {
            $this->services[$service->getSlug()] = $service;
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
     * Merge all docker-compose files from discovered services
     */
    public function compile()
    {
        $routingConfig = ['_format_version' => '1.1', 'services' => []];

        foreach ($this->mainConfiguration->getRoutes() as $routeName => $route) {
            $routingConfig['services'][] = [
                'name' => $routeName,
                'url' => $route->getTarget(),
                'routes' => [
                    'name' => $routeName,
                    'hosts' => $route->getHosts(),
                    'paths' => $route->getPaths()
                ]
            ];
        }

        if (count($this->mainConfiguration->getRoutes()) > 0) {
            $this->dockerComposeAggregator->enableRouting();
            file_put_contents(
                getcwd() . DIRECTORY_SEPARATOR . 'kong.yaml',
                Yaml::dump(
                    $this->serializer->toArray($routingConfig), 6, 2, Yaml::DUMP_NULL_AS_TILDE & Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
                )
            );
        }


        foreach ($this->getServices() as $serviceConfiguration) {
            $this->dockerComposeAggregator->attemptRegistration($serviceConfiguration);
        }

        file_put_contents(
            getcwd() . DIRECTORY_SEPARATOR . 'docker-compose.yaml',
            Yaml::dump(
                $this->serializer->toArray($this->dockerComposeAggregator->getConfiguration()),
                6,
                2,
                Yaml::DUMP_NULL_AS_TILDE
            ),
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

        $this->mainConfiguration = $this->serializer->deserialize(
            file_get_contents(getcwd() . DIRECTORY_SEPARATOR . 'awesome-project.json'),
            MainConfiguration::class,
            'json'
        );

        if (is_null($this->mainConfiguration->getServicesRoot())) {
            throw new \RuntimeException("Services root not configured");
        }

        try {
            $finder = (new Finder())
                ->depth(0)
                ->in($this->mainConfiguration->getServicesRoot());

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
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->build();
    }
}
