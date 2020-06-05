<?php

declare(strict_types=1);

namespace AwesomeProject\Aggregator;

use AwesomeProject\Model\Configuration\Constants\DockerConfiguration;
use AwesomeProject\Model\Configuration\MainConfiguration;
use AwesomeProject\Model\Configuration\ProjectConfiguration;
use AwesomeProject\Model\DockerCompose\EnvironmentVariable;
use AwesomeProject\Model\DockerCompose\PortMapping;
use AwesomeProject\Model\DockerCompose\Project;
use AwesomeProject\Model\DockerCompose\Service;
use AwesomeProject\Model\DockerCompose\Volume;

class DockerComposeAggregator
{
    /**
     * @param ProjectConfiguration[] $projectConfigurations
     * @return Project
     */
    public function aggregateConfiguration(array $projectConfigurations): Project
    {
        $mainProject = new Project(getcwd());

        foreach ($projectConfigurations as $project) {
            if (!$project->hasConfiguration(DockerConfiguration::COMPOSE_CONFIG)) {
                continue;
            }
            $this->mergeConfigurations(
                $project->getConfiguration(DockerConfiguration::COMPOSE_CONFIG),
                $mainProject,
                $awesomeGateway ?? null
            );
        }

        return $mainProject;
    }

    /**
     * Attempt service registration
     * @param Project $source
     * @param Project $target
     * @param Service|null $gateway
     */
    private function mergeConfigurations(Project $source, Project $target, ?Service $gateway)
    {
        foreach ($source->getServices() as $serviceId => $service) {
            $service->setVolumes(array_map(
                function (Volume $volume) use ($source) {
                    $hostPath = str_replace('./', $source->getPath() . DIRECTORY_SEPARATOR, $volume->getHostPath());
                    return new Volume(
                        $hostPath,
                        $volume->getContainerPath()
                    );
                },
                $service->getVolumes()
            ));

            if (is_array($service->getEnvFile())) {
                $service->setEnvFile(array_map(
                    function (string $path) use ($source) {
                        return str_replace('./', $source->getPath() . DIRECTORY_SEPARATOR, $path);
                    },
                    $service->getEnvFile()
                ));
            }

            $target->setService($serviceId, $service);

            if ($gateway) {
                $gateway->addLink($serviceId);
            }
        }

        foreach ($source->getNetworks() as $networkId => $networkConfig) {
            $target->setNetwork($networkId, $networkConfig);
            if ($gateway) {
                $gateway->addNetwork($networkId);
            }
        }
    }
}
