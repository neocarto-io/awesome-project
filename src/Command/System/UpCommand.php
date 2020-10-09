<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Command\AbstractCommand;
use AwesomeProject\Manager\DockerComposeManager;
use AwesomeProject\Manager\ProjectManager;
use AwesomeProject\Model\Configuration\Constants\DockerConfiguration;
use AwesomeProject\Model\Configuration\Constants\PHPConfiguration;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends AbstractCommand
{
    protected static $defaultName = 'up';

    private ProjectManager $projectManager;
    private DockerComposeManager $dockerComposeManager;

    /**
     * @param ProjectManager $projectManager
     * @param DockerComposeManager $dockerComposeManager
     */
    public function __construct(ProjectManager $projectManager, DockerComposeManager $dockerComposeManager)
    {
        parent::__construct();
        $this->projectManager = $projectManager;
        $this->dockerComposeManager = $dockerComposeManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Start the configuration');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projects = $this->projectManager->getProjects();

        $output->writeln(
            sprintf(
                ' => <info>%s projects discovered: %s</info>' . PHP_EOL,
                count($projects),
                implode(', ', array_keys($projects))
            )
        );


        $table = new Table($output);

        $table->setHeaders(['name', 'docker-compose', 'php composer']);

        foreach ($projects as $projectConfiguration) {
            $table->addRow(
                [
                    $projectConfiguration->getSlug(),
                    $projectConfiguration->hasConfiguration(DockerConfiguration::COMPOSE_CONFIG_PATH) ? '✔' : 'x',
                    $projectConfiguration->hasConfiguration(PHPConfiguration::COMPOSER_CONFIG) ? '✔' : 'x'
                ]
            );
        }

        $table->render();
        $output->writeln('');


        $this->dockerComposeManager->compileConfiguration();

        $this->dockerComposeManager->up()->then(
            function () use ($output) {
                $output->writeln(" => <info>Project configuration is up and running ✔️</info>");
                exit(0);
            },
            function ($errorOutputString) use ($output) {
                $output->writeln("<error>ERROR!</error>");
                $output->write($errorOutputString);
                exit(1);
            }
        );

        return 0;
    }
}
