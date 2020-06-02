<?php

declare(strict_types=1);

namespace AwesomeProject\Aggregator;

use AwesomeProject\Model\DockerCompose\EnvironmentVariable;
use AwesomeProject\Model\DockerCompose\PortMapping;
use AwesomeProject\Model\DockerCompose\Project;
use AwesomeProject\Model\DockerCompose\Service;
use AwesomeProject\Model\DockerCompose\Volume;
use AwesomeProject\Model\ServiceConfiguration;
use JMS\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

class DockerComposeAggregator
{
    private Project $globalConfiguration;
    private ?Service $awesomeGateway = null;

    private Serializer $serializer;

    /**
     * @param string $path
     * @param Serializer $serializer
     */
    public function __construct(string $path, Serializer $serializer)
    {
        $this->globalConfiguration = new Project($path);
        $this->serializer = $serializer;
    }

    /**
     * Attempt service registration
     * @param ServiceConfiguration $serviceConfiguration
     */
    public function attemptRegistration(ServiceConfiguration $serviceConfiguration)
    {
        if (is_null($serviceConfiguration->getDockerComposeConfig())) {
            return;
        }

        /** @var Project $localConfig */
        $localConfig = $this->serializer->fromArray(
            Yaml::parseFile($serviceConfiguration->getDockerComposeConfig()),
            Project::class
        );

        foreach ($localConfig->getServices() as $serviceId => $service) {
            $service->setVolumes(array_map(
                function (Volume $volume) use ($serviceConfiguration) {
                    $hostPath = str_replace('./', $serviceConfiguration->getPath() . DIRECTORY_SEPARATOR, $volume->getHostPath());
                    return new Volume(
                        $hostPath,
                        $volume->getContainerPath()
                    );
                },
                $service->getVolumes()
            ));

            if (is_array($service->getEnvFile())) {
                $service->setEnvFile(array_map(
                    function (string $path) use ($serviceConfiguration) {
                        return str_replace('./', $serviceConfiguration->getPath() . DIRECTORY_SEPARATOR, $path);
                    },
                    $service->getEnvFile()
                ));
            }

            $this->globalConfiguration->setService($serviceId, $service);

            if ($this->awesomeGateway) {
                $this->awesomeGateway->addLink($serviceId);
            }
        }

        foreach ($localConfig->getNetworks() as $networkId => $networkConfig) {
            $this->globalConfiguration->setNetwork($networkId, $networkConfig);
            if ($this->awesomeGateway) {
                $this->awesomeGateway->addNetwork($networkId);
            }
        }
    }

    /**
     * Enable routing service
     */
    public function enableRouting()
    {
        $this->awesomeGateway = new Service();
        $this->awesomeGateway
            ->setImage('kong:latest')
            ->setEnvironment(
                [
                    new EnvironmentVariable('KONG_DATABASE', 'off'),
                    new EnvironmentVariable('KONG_DECLARATIVE_CONFIG', '/configs/kong-dev.yml'),
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
            ->setLinks([])
            ->setNetworks([]);

        $this->globalConfiguration->setService('awesome-http-gateway', $this->awesomeGateway);
    }

    /**
     * @return Project
     */
    public function getConfiguration(): Project
    {
        return $this->globalConfiguration;
    }
}
