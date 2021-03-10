<?php

declare(strict_types=1);

namespace AwesomeProject\Aggregator;

use AwesomeProject\Model\RootProject;
use JMS\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;
use AwesomeProject\Model\DockerCompose as DockerCompose;

class DockerComposeAggregator
{
    public function __construct(private RootProject $rootProject, private Serializer $serializer)
    {
    }

    /**
     * @return DockerCompose\Project
     */
    public function getAggregatedProject(): DockerCompose\Project
    {
        $mainProject = new DockerCompose\Project(getcwd());

        foreach ($this->rootProject->getProjects() as $project) {
            if (!$project->isDockerCompose()) {
                continue;
            }

            /** @var DockerCompose\Project $dockerComposeConfiguration */
            $dockerComposeConfiguration = $this->serializer->fromArray(
                Yaml::parseFile($project->getDockerComposePath()),
                DockerCompose\Project::class
            );
            $dockerComposeConfiguration->setPath($project->getPath());

            $this->mergeConfigurations($dockerComposeConfiguration, $mainProject);
        }

        return $mainProject;
    }

    /**
     * @param DockerCompose\Project $source
     * @param DockerCompose\Project $target
     */
    private function mergeConfigurations(DockerCompose\Project $source, DockerCompose\Project $target)
    {
        foreach ($source->getServices() as $serviceId => $service) {
            if (is_array($service->getVolumes())) {
                $this->translateVolumePaths($service, $source);
            }

            if (is_array($service->getEnvFile())) {
                $this->translateEnvFilePaths($service, $source);
            }

            $target->setService($serviceId, $service);
        }

        foreach ($source->getNetworks() as $networkId => $networkConfig) {
            $target->setNetwork($networkId, $networkConfig);
        }
    }

    /**
     * @param DockerCompose\Service $service
     * @param DockerCompose\Project $source
     */
    private function translateVolumePaths(DockerCompose\Service $service, DockerCompose\Project $source)
    {
        $service->setVolumes(
            array_map(
                function (DockerCompose\Volume $volume) use ($source) {
                    //absolute path
                    if ($volume->getHostPath()[0] == '/') {
                        $hostPath = $volume->getHostPath();
                    } else {
                        $hostPath = $source->getPath() . DIRECTORY_SEPARATOR . $volume->getHostPath();
                    }

                    if (is_dir($hostPath) || is_file($hostPath)) {
                        $hostPath = realpath($hostPath);
                    }

                    return new DockerCompose\Volume(
                        $hostPath,
                        $volume->getContainerPath()
                    );
                },
                $service->getVolumes()
            )
        );
    }

    /**
     * @param DockerCompose\Service $service
     * @param DockerCompose\Project $source
     */
    private function translateEnvFilePaths(DockerCompose\Service $service, DockerCompose\Project $source)
    {
        $service->setEnvFile(
            array_map(
                function (string $path) use ($source) {
                    //absolute path
                    if ($path[0] != '/') {
                        $path = $source->getPath() . DIRECTORY_SEPARATOR . $path;
                    }

                    if (is_dir($path) || is_file($path)) {
                        return realpath($path);
                    }

                    return $path;
                },
                $service->getEnvFile()
            )
        );
    }
}
