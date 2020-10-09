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
    private const PUBLIC_HTTP_PORT = '80';
    private const PUBLIC_HTTPS_PORT = '443';

    private const ADMIN_HTTP_PORT = '8001';
    private const ADMIN_HTTPS_PORT = '8444';

    private const CONTAINER_PUBLIC_HTTP_PORT = '8000';
    private const CONTAINER_PUBLIC_HTTPS_PORT = '8443';

    private const CONTAINER_ADMIN_HTTP_PORT = '8001';
    private const CONTAINER_ADMIN_HTTPS_PORT = '8444';

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
                    new EnvironmentVariable('KONG_DECLARATIVE_CONFIG', '/configs/config.yaml'),
                    new EnvironmentVariable('KONG_PROXY_ACCESS_LOG', '/dev/stdout'),
                    new EnvironmentVariable('KONG_ADMIN_ACCESS_LOG', '/dev/stdout'),
                    new EnvironmentVariable('KONG_PROXY_ERROR_LOG', '/dev/stderr'),
                    new EnvironmentVariable('KONG_ADMIN_ERROR_LOG', '/dev/stderr'),
                    new EnvironmentVariable(
                        'KONG_ADMIN_LISTEN',
                        sprintf(
                            '0.0.0.0:%s, 0.0.0.0:%s ssl',
                            self::CONTAINER_ADMIN_HTTP_PORT,
                            self::CONTAINER_ADMIN_HTTPS_PORT
                        )
                    ),
                ]
            )
            ->setPorts(
                [
                    new PortMapping(self::PUBLIC_HTTP_PORT, self::CONTAINER_PUBLIC_HTTP_PORT),
                    new PortMapping(self::PUBLIC_HTTPS_PORT, self::CONTAINER_PUBLIC_HTTPS_PORT),
                    new PortMapping(self::ADMIN_HTTP_PORT, self::CONTAINER_ADMIN_HTTP_PORT),
                    new PortMapping(self::ADMIN_HTTPS_PORT, self::CONTAINER_ADMIN_HTTPS_PORT),
                ]
            )
            ->setLinks(array_keys($project->getServices()))
            ->setNetworks(array_keys($project->getNetworks()))
            ->setVolumes(
                [
                    new Volume(
                        $this->compileConfiguration(),
                        '/configs'
                    ),
                ]
            );

        $project->setService(
            'awesome-http-gateway',
            $awesomeGateway
        );
    }

    /**
     * @return string dir path of the configuration
     */
    private function compileConfiguration(): string
    {
        $projectConfig = $this->projectManager->getMainConfiguration();

        $routingConfig = ['_format_version' => '1.1', 'services' => []];

        foreach ($projectConfig->getRoutes() as $routeName => $route) {
            $routingConfig['services'][] = [
                'name' => $routeName,
                'url' => $route->getTarget(),
                'routes' => [
                    [
                        'name' => $routeName,
                        'hosts' => $route->getHosts(),
                        'paths' => $route->getPaths(),
                    ],
                ],
            ];
        }

        $kongConfigDir = sprintf('/tmp/%s-awesome-project/kong', md5(getcwd()));

        if (!is_dir($kongConfigDir)) {
            mkdir($kongConfigDir, 0755, true);
        }


        file_put_contents(
            "$kongConfigDir/config.yaml",
            Yaml::dump(
                $this->serializer->toArray($routingConfig),
                6,
                2,
                Yaml::DUMP_NULL_AS_TILDE & Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
            )
        );

        return $kongConfigDir;
    }
}
