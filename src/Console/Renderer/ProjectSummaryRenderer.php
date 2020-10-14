<?php

declare(strict_types=1);

namespace AwesomeProject\Console\Renderer;

use AwesomeProject\Manager\ProjectManager;
use AwesomeProject\Model\Configuration\Constants\DockerConfiguration;
use AwesomeProject\Model\Configuration\Constants\GitConfiguration;
use AwesomeProject\Model\Configuration\Constants\PHPConfiguration;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectSummaryRenderer
{
    private ProjectManager $projectManager;

    /**
     * @param ProjectManager $projectManager
     */
    public function __construct(ProjectManager $projectManager)
    {
        $this->projectManager = $projectManager;
    }

    /**
     * @param OutputInterface $output
     */
    public function render(OutputInterface $output)
    {

        $projects = $this->projectManager->getProjectStates();

        $output->writeln(
            sprintf(
                '=> [<info>INFO</info>] %s projects discovered: <info>%s</info>' . PHP_EOL,
                count($projects),
                implode(', ', array_keys($projects))
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

        foreach ($projects as $projectConfiguration) {

            $capabilities = [];

            if ($projectConfiguration->hasConfiguration(GitConfiguration::BRANCH) && $output->isVeryVerbose()) {
                $capabilities[] = 'git';
            }


            if ($projectConfiguration->hasConfiguration(
                    PHPConfiguration::COMPOSER_CONFIG_PATH
                ) && $output->isVeryVerbose()) {
                $capabilities[] = 'composer';
            }

            if ($projectConfiguration->hasConfiguration(DockerConfiguration::COMPOSE_CONFIG_PATH)) {
                $capabilities[] = '<info>docker-compose</info>';
            }

            $row = [
                "<info>{$projectConfiguration->getSlug()}</info>",
                $this->projectManager->getMainConfiguration()->getProject(
                    $projectConfiguration->getSlug()
                ) ? 'external' : 'embedded',
            ];
            if ($output->isVeryVerbose()) {
                $row[] = $projectConfiguration->getConfiguration(GitConfiguration::ORIGIN_URL);
            }

            array_push(
                $row,
                $projectConfiguration->hasConfiguration(
                    GitConfiguration::STATE
                ) ? $projectConfiguration->getConfiguration(GitConfiguration::STATE) : '',
                implode(', ', $capabilities)

            );

            $table->addRow($row);
        }

        $table->render();
        $output->writeln('');
    }
}
