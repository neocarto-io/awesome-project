<?php

declare(strict_types=1);

namespace AwesomeProject\Console\Renderer;

use AwesomeProject\Manager\GitManager;
use AwesomeProject\Model\Configuration\Constants\GitConfiguration;
use AwesomeProject\Model\RootProject;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectSummaryRenderer
{
    public function __construct(private RootProject $rootProject, private GitManager $gitManager)
    {
    }

    /**
     * @param OutputInterface $output
     */
    public function render(OutputInterface $output)
    {
        $projectNames = $this->rootProject->getProjectNames();
        $output->writeln(
            sprintf(
                '=> [<info>INFO</info>] %s projects discovered: <info>%s</info>' . PHP_EOL,
                count($projectNames),
                implode(', ', $projectNames)
            )
        );


        if (!$output->isVerbose()) {
            return;
        }

        $table = new Table($output);

        if ($output->isVeryVerbose()) {
            $table->setHeaders(['name', 'type', 'source', 'version', 'capabilities']);
        } else {
            $table->setHeaders(['name', 'type', 'version', 'capabilities']);
        }

        foreach ($projectNames as $projectName) {
            $project = $this->rootProject->getProject($projectName);

            $row = [
                "<info>{$projectName}</info>",
                $project->getSource() ? 'external' : 'embedded',
            ];

            $capabilities = [];

            if ($output->isVeryVerbose()) {
                if ($project->isGit()) {
                    $capabilities[] = 'git';
                    $row[] = $this->gitManager->getOrigin($project->getPath());
                    $row[] = $this->gitManager->getState($project->getPath());
                } else {
                    $row[] = '';
                    $row[] = '';
                }
                if ($project->isPhpComposer()) {
                    $capabilities[] = 'php-composer';
                }
            }

            if ($project->isDockerCompose()) {
                $capabilities[] = '<info>docker-compose</info>';
            }

            $row[] = implode(', ', $capabilities);

            $table->addRow($row);
        }

        $table->render();
        $output->writeln('');
    }
}
