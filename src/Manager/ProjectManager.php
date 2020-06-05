<?php

namespace AwesomeProject\Manager;

use AwesomeProject\Aggregator\ProjectAggregator;
use AwesomeProject\Model\Configuration\MainConfiguration;
use AwesomeProject\Model\Configuration\ProjectConfiguration;
use AwesomeProject\Serializer\DockerComposeSerializerHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ProjectManager
{
    private ProjectAggregator $projectAggregator;

    /** @var ProjectConfiguration[]|null */
    private ?array $projects = null;

    public function __construct(ProjectAggregator $projectAggregator)
    {
        $this->projectAggregator = $projectAggregator;
    }

    /**
     * @return ProjectConfiguration[]
     */
    public function getProjects(): array
    {
        if (is_null($this->projects)) {
            $this->projects = [];
            foreach ($this->projectAggregator->getProjects() as $project) {
                $this->projects[$project->getSlug()] = $project;
            }
        }

        return $this->projects;
    }

    public function getMainConfiguration(): MainConfiguration
    {
        return $this->projectAggregator->getConfiguration();
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


        foreach ($this->getProjects() as $serviceConfiguration) {
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
     * @return \Iterator|ProjectConfiguration[]
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

        if (is_null($this->mainConfiguration->getProjectsRoot())) {
            throw new \RuntimeException("Services root not configured");
        }

        try {
            $finder = (new Finder())
                ->depth(0)
                ->in($this->mainConfiguration->getProjectsRoot());

            foreach ($finder->directories() as $fileInfo) {
                yield new ProjectConfiguration(realpath($fileInfo->getPathname()));
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
