<?php

declare(strict_types=1);

namespace AwesomeProject\Aggregator;

use AwesomeProject\Manager\GitManager;
use AwesomeProject\Model\Configuration\Constants\DockerConfiguration;
use AwesomeProject\Model\Configuration\Constants\GitConfiguration;
use AwesomeProject\Model\Configuration\Constants\PHPConfiguration;
use AwesomeProject\Model\Configuration\MainConfiguration;
use AwesomeProject\Model\Configuration\ProjectState;
use AwesomeProject\Model\DockerCompose\Project;
use JMS\Serializer\Serializer;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ProjectAggregator
{
    private Serializer         $serializer;
    private ?MainConfiguration $configuration = null;
    private GitManager         $gitManager;

    /**
     * @param Serializer $serializer
     * @param GitManager $gitManager
     */
    public function __construct(Serializer $serializer, GitManager $gitManager)
    {
        $this->serializer = $serializer;
        $this->gitManager = $gitManager;
    }

    /**
     * @return MainConfiguration
     */
    public function getConfiguration(): MainConfiguration
    {
        if (is_array($this->configuration)) {
            return $this->configuration;
        }

        if (!file_exists($manifestPath = getcwd() . "/awesome-project.yaml")) {
            throw new \RuntimeException("Not an awesome project :( !");
        }

        $this->configuration = $this->serializer->fromArray(
            Yaml::parseFile($manifestPath),
            MainConfiguration::class
        );

        return $this->configuration;
    }

    /**
     * @return \Iterator|ProjectState[]
     */
    public function getProjects(): \Iterator
    {
        if (is_null($this->getConfiguration()->getProjectsRoot())) {
            throw new \RuntimeException("Projects root not configured");
        }

        try {
            $finder = (new Finder())
                ->depth(0)
                ->in($this->getConfiguration()->getProjectsRoot());

            foreach ($finder->directories() as $fileInfo) {
                yield $this->autoDiscoverConfiguration(
                    new ProjectState(realpath($fileInfo->getPathname()))
                );
            }
        } catch (DirectoryNotFoundException $exception) {
            throw new \RuntimeException("Projects root not valid");
        }
    }

    /**
     * Default auto-discovery
     *
     * @param ProjectState $projectConfiguration
     * @return ProjectState
     */
    private function autoDiscoverConfiguration(ProjectState $projectConfiguration): ProjectState
    {
        if (file_exists($dockerComposeConfigPath = "{$projectConfiguration->getPath()}/docker-compose.yaml")) {
            $projectConfiguration->setConfiguration(DockerConfiguration::COMPOSE_CONFIG_PATH, $dockerComposeConfigPath);
            /** @var Project $dockerComposeConfig */
            $projectConfiguration->setConfiguration(
                DockerConfiguration::COMPOSE_CONFIG,
                $dockerComposeConfig = $this->serializer->fromArray(
                    Yaml::parseFile($dockerComposeConfigPath),
                    Project::class
                )
            );

            $dockerComposeConfig->setPath($projectConfiguration->getPath());
        }
        if (file_exists($phpComposerConfigPath = "{$projectConfiguration->getPath()}/composer.json")) {
            $projectConfiguration->setConfiguration(PHPConfiguration::COMPOSER_CONFIG_PATH, $phpComposerConfigPath);
            /*$projectConfiguration->setConfiguration(
                PHPConfiguration::COMPOSER_CONFIG,
                json_decode(file_get_contents($phpComposerConfigPath))
            );*/
        }

        if ($this->gitManager->isGit($projectConfiguration->getPath())) {
            $projectConfiguration
                ->setConfiguration(
                    GitConfiguration::ORIGIN_URL,
                    $this->gitManager->getOrigin($projectConfiguration->getPath())
                )
                ->setConfiguration(
                    GitConfiguration::BRANCH,
                    $this->gitManager->getBranch($projectConfiguration->getPath())
                )
                ->setConfiguration(
                    GitConfiguration::STATE,
                    $this->gitManager->getState($projectConfiguration->getPath())
                );
        }

        return $projectConfiguration;
    }

    public function reset()
    {
        $this->configuration = null;
    }
}
