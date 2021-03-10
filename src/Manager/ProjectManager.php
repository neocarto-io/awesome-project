<?php

declare(strict_types=1);

namespace AwesomeProject\Manager;

use AwesomeProject\Model\Manifest\MainManifest;
use AwesomeProject\Model\Manifest\ProjectSource;
use AwesomeProject\Model\Project;
use AwesomeProject\Model\RootProject;
use AwesomeProject\Traits\ProcessControlTrait;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectManager
{
    use ProcessControlTrait;

    public function __construct(
        private MainManifest $manifest,
        private RootProject $rootProject,
        private GitManager $gitManager
    ) {
    }

    public function upsertProjects(OutputInterface $output)
    {
        $installed = [];
        foreach ($this->manifest->getProjects()->getSettings() as $projectName => $projectSettings) {
            if ($this->rootProject->getProject($projectName)) {
                //installed/detected
                continue;
            }
            $this->installProject($projectName, $projectSettings->getSource(), $output);
            $installed[] = $projectName;
        }

        foreach ($this->rootProject->getProjects() as $projectName => $project) {
            if (in_array($projectName, $installed)) {
                continue;
            }

            $this->updateProject($project, $output);
            $this->installDependencies($project, $output);
        }
    }

    /**
     * @param string $projectName
     * @param ProjectSource $source
     * @param OutputInterface $output
     */
    public function installProject(string $projectName, ProjectSource $source, OutputInterface $output)
    {
        $output->writeln("=> [<info>INFO</info>] Installing <info>{$projectName}</info> ...");

        switch ($source->getType()) {
            case 'git':
                $result = $this->execute(
                    ['git', 'clone', $source->getPath(), $projectName],
                    ['workingDirectory' => realpath($this->manifest->getProjects()->getRoot())]
                );
                break;
            default:
                $result = null;

        }

        if ($result) {
            $output->writeln("=> [<info>INFO</info>] Installed <info>{$projectName}</info> ...");
        } else {
            $output->writeln("=> [<error>ERROR</error>] Cannot install {$projectName}");
        }


        $output->write(PHP_EOL);
    }


    /**
     * @param Project $project
     * @param OutputInterface $output
     */
    public function updateProject(Project $project, OutputInterface $output)
    {
        $originUrl = $this->gitManager->getOrigin($project->getPath());

        if (is_null($originUrl)) {
            return;
        }

        $result = $this->execute(['git', 'pull'], ['workingDirectory' => $project->getPath()]);

        if ($result) {
            $output->writeln("=> [<info>INFO</info>][<info>{$project->getName()}</info>] Fetched latest sources");
        } else {
            $output->writeln("=> [<comment>WARN</comment>][<info>{$project->getName()}</info>] Could not fetch latest sources");
        }
    }

    /**
     * @param Project $project
     * @param OutputInterface $output
     */
    public function installDependencies(Project $project, OutputInterface $output)
    {
        if (!$project->isPhpComposer()) {
            return;
        }

        $result = $this->execute(
            ['composer', 'install'], ['workingDirectory' => $project->getPath()]
        );

        if ($result) {
            $output->writeln("=> [<info>INFO</info>][<info>{$project->getName()}</info>] Dependencies installed");
        } else {
            $output->writeln("=> [<comment>WARN</comment>][<info>{$project->getName()}</info>] Could not install dependencies");
        }
    }
}
