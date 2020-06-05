<?php

declare(strict_types=1);

namespace AwesomeProject\Aggregator;

use AwesomeProject\Manager\ProjectManager;
use AwesomeProject\Model\DockerCompose\EnvironmentVariable;
use AwesomeProject\Model\DockerCompose\PortMapping;
use AwesomeProject\Model\DockerCompose\Project;
use AwesomeProject\Model\DockerCompose\Service;
use AwesomeProject\Model\DockerCompose\Volume;
use JMS\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

class HttpGatewayAggregator
{
    private ProjectManager $projectManager;
    private Serializer $serializer;

    /**
     * @param ProjectManager $projectManager
     * @param Serializer $serializer
     */
    public function __construct(ProjectManager $projectManager, Serializer $serializer)
    {
        $this->projectManager = $projectManager;
        $this->serializer = $serializer;
    }

    /**
     * @param Project $project
     */
    public function attachHttpGatewayService(Project $project): void
    {
        $this->compileConfiguration();

        $awesomeGateway = new Service();
        $awesomeGateway
            ->setImage('kong:latest')
            ->setEnvironment(
                [
                    new EnvironmentVariable('KONG_DATABASE', 'off'),
                    new EnvironmentVariable('KONG_DECLARATIVE_CONFIG', '/configs/config.yml'),
                    new EnvironmentVariable('KONG_PROXY_ACCESS_LOG', '/dev/stdout'),
                    new EnvironmentVariable('KONG_ADMIN_ACCESS_LOG', '/dev/stdout'),
                    new EnvironmentVariable('KONG_PROXY_ERROR_LOG', '/dev/stderr'),
                    new EnvironmentVariable('KONG_ADMIN_ERROR_LOG', '/dev/stderr'),
                    new EnvironmentVariable('KONG_ADMIN_LISTEN', '0.0.0.0:8001, 0.0.0.0:8444 ssl'),
                ]
            )
            ->setPorts(
                [
                    new PortMapping(80, 8000),
                    new PortMapping(443, 8443),
                    new PortMapping(8001, 8001),
                    new PortMapping(8444, 8444),
                ]
            )
            ->setLinks(array_keys($project->getServices()))
            ->setNetworks(array_keys($project->getNetworks()))
            ->setVolumes(
                [
                    new Volume(
                        $this->compileConfiguration(),
                        '/configs'
                    )
                ]
            );

        $project->setService(
            'awesome-http-gateway',
            $awesomeGateway
        );
    }

    private function compileConfiguration(): string
    {
        $projectConfig = $this->projectManager->getMainConfiguration();

        $routingConfig = ['_format_version' => '1.1', 'services' => []];

        foreach ($projectConfig->getRoutes() as $routeName => $route) {
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

        $kongConfigDir = sprintf('/tmp/%s-awesome-project/kong', md5(getcwd()));

        if (!is_dir($kongConfigDir)) {
            mkdir(dirname($kongConfigDir), 0777, true);
        }


        file_put_contents(
            "$kongConfigDir/config.yaml",
            Yaml::dump(
                $this->serializer->toArray($routingConfig), 6, 2, Yaml::DUMP_NULL_AS_TILDE & Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
            )
        );

        return $kongConfigDir;
    }
}