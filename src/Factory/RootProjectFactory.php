<?php

declare(strict_types=1);

namespace AwesomeProject\Factory;

use AwesomeProject\Model\Manifest\MainManifest;
use AwesomeProject\Model\Project;
use AwesomeProject\Model\RootProject;
use Symfony\Component\Finder\Finder;

class RootProjectFactory
{
    /**
     * @param MainManifest $manifest
     */
    public function __construct(private MainManifest $manifest)
    {
    }

    /**
     * @return RootProject
     */
    public function createRootProject(): RootProject
    {
        $rootProject = new RootProject();

        $finder = (new Finder())
            ->depth(0)
            ->in($this->manifest->getProjects()->getRoot());

        foreach ($finder->directories() as $directoryInfo) {
            $projectName = $directoryInfo->getFilename();
            $project = new Project(
                $projectName,
                $directoryInfo->getRealPath(),
                $this->manifest->getProjects()->getSource($projectName)
            );

            $rootProject->addProject($project);
        }

        return $rootProject;
    }
}
