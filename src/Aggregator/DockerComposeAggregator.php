<?php

declare(strict_types=1);

namespace AwesomeProject\Aggregator;

use AwesomeProject\Model\Manifest\MainManifest;
use AwesomeProject\Model\RootProject;
use JMS\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;
use AwesomeProject\Model\DockerCompose as DockerCompose;

class DockerComposeAggregator
{
    /**
     * @param MainManifest $manifest
     * @param RootProject $rootProject
     * @param Serializer $serializer
     */
    public function __construct(
        private MainManifest $manifest,
        private RootProject $rootProject,
        private Serializer $serializer
    ) {
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
                $this->translateVolumePaths($serviceId, $service, $source);
            }

            if (is_array($service->getEnvFile())) {
                $this->translateEnvFilePaths($service, $source);
            }

            if ($this->manifest->getLogging()->hasService($serviceId)) {
                $service
                    ->addDependsOn($this->manifest->getLogging()->getService(), 'service_healthy')
                    ->setLogging(
                        [
                            'driver' => $this->manifest->getLogging()->getDriver(),
                            'options' => $this->manifest->getLogging()->getOptions()
                        ]
                    );
            }

            //todo: merge properties

            $target->setService($serviceId, $service);
        }

        foreach ($source->getNetworks() as $networkId => $networkConfig) {
            $target->setNetwork($networkId, $networkConfig);
        }
    }

    /**
     * @param string $serviceId
     * @param DockerCompose\Service $service
     * @param DockerCompose\Project $source
     */
    private function translateVolumePaths(
        string $serviceId,
        DockerCompose\Service $service,
        DockerCompose\Project $source
    ) {
        $finalVolumes = [];

        foreach ($service->getVolumes() as $volume) {
            $volume = $this->translateVolume($volume, $source->getPath());

            $finalVolumes[$volume->getContainerPath()] = $volume;
        }

        //todo: overwrite all properties
        $serviceOverwrite = $this->manifest->getDockerCompose()->getGlobalService($serviceId);

        if (is_null($serviceOverwrite)) {
            $volumes = [];
        } else {
            $volumes = $serviceOverwrite->getVolumes();
        }

        foreach ($volumes as $volume) {
            $volume = $this->translateVolume($volume, getcwd());

            $finalVolumes[$volume->getContainerPath()] = $volume;
        }

        $service->setVolumes(array_values($finalVolumes));
    }

    /**
     * @param DockerCompose\Volume $volume
     * @param string $basePath
     * @return DockerCompose\Volume
     */
    private function translateVolume(DockerCompose\Volume $volume, string $basePath): DockerCompose\Volume
    {
        if ($volume->getHostPath()[0] == '/') {
            $hostPath = $volume->getHostPath();
        } else {
            $hostPath = $basePath . DIRECTORY_SEPARATOR . $volume->getHostPath();
        }

        if (is_dir($hostPath) || is_file($hostPath)) {
            $hostPath = realpath($hostPath);
        }

        return new DockerCompose\Volume(
            $hostPath,
            $volume->getContainerPath()
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
