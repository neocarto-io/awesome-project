<?php

declare(strict_types=1);

namespace AwesomeProject\Aggregator;

use AwesomeProject\Model\DockerCompose\EnvironmentVariable;
use AwesomeProject\Model\DockerCompose\PortMapping;
use AwesomeProject\Model\DockerCompose\Project;
use AwesomeProject\Model\DockerCompose\Service;
use AwesomeProject\Model\DockerCompose\Volume;
use AwesomeProject\Model\Manifest\MainManifest;
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

    /**
     * @param MainManifest $manifest
     * @param Serializer $serializer
     */
    public function __construct(private MainManifest $manifest, private Serializer $serializer)
    {
    }

    /**
     * @param Project $project
     */
    public function attachHttpGatewayService(Project $project): void
    {
        $kongConfig = getcwd() . "/kong";

        if (!is_dir($kongConfig)) {
            mkdir($kongConfig, 0755, true);
        };

        $gatewayTargets = $this->compileConfiguration($kongConfig);

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
                            $this->manifest->getHttp()->getPort('admin_http') ?? self::ADMIN_HTTP_PORT,
                            $this->manifest->getHttp()->getPort('admin_https') ?? self::ADMIN_HTTPS_PORT
                        )
                    ),
                ]
            )
            ->setPorts(
                [
                    new PortMapping(
                        $this->manifest->getHttp()->getPort('http') ?? self::PUBLIC_HTTP_PORT,
                        self::CONTAINER_PUBLIC_HTTP_PORT
                    ),
                    new PortMapping(
                        $this->manifest->getHttp()->getPort('https') ?? self::PUBLIC_HTTPS_PORT,
                        self::CONTAINER_PUBLIC_HTTPS_PORT
                    ),
                    new PortMapping(
                        $this->manifest->getHttp()->getPort('admin_http') ?? self::ADMIN_HTTP_PORT,
                        self::CONTAINER_ADMIN_HTTP_PORT
                    ),
                    new PortMapping(
                        $this->manifest->getHttp()->getPort('admin_https') ?? self::ADMIN_HTTPS_PORT,
                        self::CONTAINER_ADMIN_HTTPS_PORT
                    ),
                ]
            )
            ->setLinks($gatewayTargets)
            ->setNetworks(array_keys($project->getNetworks()))
            ->setVolumes([new Volume($kongConfig, '/configs')]);

        $project->setService(
            'awesome-http-gateway',
            $awesomeGateway
        );
    }

    /**
     * @param string $kongConfig
     * @return array
     */
    private function compileConfiguration(string $kongConfig): array
    {
        $routingConfig = ['_format_version' => '1.1', 'services' => []];
        $aggregatedHosts = [];

        foreach ($this->manifest->getHttp()->getRoutes() as $target => $sources) {

            $targetHost = parse_url("http://$target", PHP_URL_HOST);

            if (is_null($targetHost)) {
                $targetHost = $target;
            }
            if (!in_array($targetHost, $aggregatedHosts)) {
                $aggregatedHosts[] = $targetHost;
            }

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

        file_put_contents(
            "{$kongConfig}/config.yaml",
            Yaml::dump(
                $this->serializer->toArray($routingConfig),
                6,
                2,
                Yaml::DUMP_NULL_AS_TILDE & Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
            )
        );

        return $aggregatedHosts;
    }
}
