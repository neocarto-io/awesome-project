<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Manager\ProjectManager;
use AwesomeProject\Traits\DockerComposeAwareTrait;
use AwesomeProject\Traits\ProjectSummaryRendererTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    use ProjectSummaryRendererTrait, DockerComposeAwareTrait;

    protected static $defaultName = 'install';

    public function __construct(private ProjectManager $projectManager)
    {
        parent::__construct();
    }


    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Install services');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->projectSummaryRenderer->render($output);

        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->projectManager->upsertProjects($output);

        if ($this->dockerComposeManager->up($output)) {
            $output->writeln("=> [<info>INFO</info>] Project configuration is up and running ✔");
            return 0;
        } else {
            $output->writeln("=> [<error>ERROR</error>]  Cannot start docker-compose configuration");
            return 1;
        }
    }
}
