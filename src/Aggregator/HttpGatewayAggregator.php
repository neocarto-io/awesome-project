<?php

declare(strict_types=1);

namespace AwesomeProject\Aggregator;

use AwesomeProject\Manager\ProjectManager;
use AwesomeProject\Model\Configuration\MainConfiguration;
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

        $projectConfig = $this->projectManager->getMainConfiguration();

        $this->compileConfiguration($projectConfig);

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
                            $projectConfig->getPort('admin_http') ?? self::ADMIN_HTTP_PORT,
                            $projectConfig->getPort('admin_https') ?? self::ADMIN_HTTPS_PORT
                        )
                    ),
                ]
            )
            ->setPorts(
                [
                    new PortMapping(
                        $projectConfig->getPort('http') ?? self::PUBLIC_HTTP_PORT,
                        self::CONTAINER_PUBLIC_HTTP_PORT
                    ),
                    new PortMapping(
                        $projectConfig->getPort('https') ?? self::PUBLIC_HTTPS_PORT,
                        self::CONTAINER_PUBLIC_HTTPS_PORT
                    ),
                    new PortMapping(
                        $projectConfig->getPort('admin_http') ?? self::ADMIN_HTTP_PORT,
                        self::CONTAINER_ADMIN_HTTP_PORT
                    ),
                    new PortMapping(
                        $projectConfig->getPort('admin_https') ?? self::ADMIN_HTTPS_PORT,
                        self::CONTAINER_ADMIN_HTTPS_PORT
                    ),
                ]
            )
            ->setLinks(array_keys($project->getServices()))
            ->setNetworks(array_keys($project->getNetworks()))
            ->setVolumes(
                [
                    new Volume(
                        $this->compileConfiguration($projectConfig),
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
     * @param MainConfiguration $projectConfig
     * @return string dir path of the configuration
     */
    private function compileConfiguration(MainConfiguration $projectConfig): string
    {

        $routingConfig = ['_format_version' => '1.1', 'services' => []];

        foreach ($projectConfig->getRoutes() as $target => $sources) {

            $name = str_replace([':', '/'], ['-', '_'], $target);

            $hosts = [];
            $paths = [];

            foreach ($sources as $source) {
                $source = "http://$source";
                $paths[] = parse_url($source, PHP_URL_PATH);
                $hosts[] = parse_url($source, PHP_URL_HOST);
            }

            $routingConfig['services'][] = [
                'name' => $name,
                'url' => "http://$target",
                'routes' => [
                    [
                        'name' => $name,
                        'hosts' => $hosts,
                        'paths' => $paths,
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
