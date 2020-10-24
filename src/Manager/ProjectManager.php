<?php

namespace AwesomeProject\Manager;

use AwesomeProject\Aggregator\ProjectAggregator;
use AwesomeProject\Model\Configuration\Constants\GitConfiguration;
use AwesomeProject\Model\Configuration\Constants\PHPConfiguration;
use AwesomeProject\Model\Configuration\MainConfiguration;
use AwesomeProject\Model\Configuration\ProjectConfiguration;
use AwesomeProject\Model\Configuration\ProjectState;
use AwesomeProject\Traits\ProcessControlTrait;
use Hoa\Stream\IStream\Out;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class ProjectManager
{
    use ProcessControlTrait;

    private ProjectAggregator $projectAggregator;

    /** @var ProjectState[]|null */
    private ?array $projectStates = null;

    /**
     * @param ProjectAggregator $projectAggregator
     */
    public function __construct(ProjectAggregator $projectAggregator)
    {
        $this->projectAggregator = $projectAggregator;
    }

    /**
     * @return ProjectState[]
     */
    public function getProjectStates(): array
    {
        if (is_null($this->projectStates)) {
            $this->projectStates = [];
            foreach ($this->projectAggregator->getProjects() as $project) {
                $this->projectStates[$project->getSlug()] = $project;
            }
        }

        return $this->projectStates;
    }

    /**
     * @param string $slug
     * @return ProjectState|null
     */
    public function getProjectState(string $slug): ?ProjectState
    {
        if (is_null($this->projectStates)) {
            $this->getProjectStates();
        }
        return $this->projectStates[$slug] ?? null;
    }

    /**
     * @return MainConfiguration
     */
    public function getMainConfiguration(): MainConfiguration
    {
        return $this->projectAggregator->getConfiguration();
    }

    /**
     * @param OutputInterface $output
     */
    public function upsertProjects(OutputInterface $output)
    {
        $justInstalled = [];
        foreach ($this->getMainConfiguration()->getProjects() as $slug => $project) {
            if ($this->getProjectState($slug)) {
                continue;
            }
            $this->installProject($slug, $project, $output);
            $justInstalled[$slug] = true;
        }


        $this->projectStates = null;
        $this->projectAggregator->reset();

        foreach ($this->getProjectStates() as $slug => $projectState) {
            if (isset($justInstalled[$slug])) {
                continue;
            }
            $this->updateProject($slug, $output);
            $this->installDependencies($slug, $output);
        }
    }

    /**
     * @param string $slug
     * @param ProjectConfiguration $projectConfiguration
     * @param OutputInterface $output
     */
    private function installProject(string $slug, ProjectConfiguration $projectConfiguration, OutputInterface $output)
    {
        $output->writeln("=> [<info>INFO</info>] Installing <info>{$slug}</info> ...");

        $result = $this->execute(
            ['git', 'clone', $projectConfiguration->getSource(), $slug],
            ['workingDirectory' => realpath($this->getMainConfiguration()->getProjectsRoot())]
        );

        if ($result) {
            $output->writeln("=> [<info>INFO</info>] Installed <info>{$slug}</info> ...");
        } else {
            $output->writeln("=> [<error>ERROR</error>] Cannot install {$slug}");
        }


        $output->write(PHP_EOL);
    }

    /**
     * @param string $slug
     * @param OutputInterface $output
     */
    public function updateProject(string $slug, OutputInterface $output)
    {
        $state = $this->getProjectState($slug);

        if (!$state->hasConfiguration(GitConfiguration::ORIGIN_URL)) {
            return;
        }

        $result = $this->execute(['git', 'pull'], ['workingDirectory' => $state->getPath()]);

        if ($result) {
            $output->writeln("=> [<info>INFO</info>][<info>{$slug}</info>] Fetched latest sources");
        } else {
            $output->writeln("=> [<comment>WARN</comment>][<info>{$slug}</info>] Could not fetch latest sources");
        }
    }

    public function installDependencies(string $slug, OutputInterface $output)
    {
        $state = $this->getProjectState($slug);

        if (!$state->hasConfiguration(PHPConfiguration::COMPOSER_CONFIG_PATH)) {
            return;
        }

        $result = $this->execute(
            ['composer', 'install'], ['workingDirectory' => $state->getPath()]
        );

        if ($result) {
            $output->writeln("=> [<info>INFO</info>][<info>{$slug}</info>] Dependencies installed");
        } else {
            $output->writeln("=> [<comment>WARN</comment>][<info>{$slug}</info>] Could not install dependencies");
        }
    }
}
