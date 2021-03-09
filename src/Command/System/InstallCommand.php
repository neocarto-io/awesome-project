<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Traits\DockerComposeAwareTrait;
use AwesomeProject\Traits\ProjectSummaryRendererTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
    use ProjectSummaryRendererTrait, DockerComposeAwareTrait;

    protected static $defaultName = 'install';

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
        /*$this->projectManager->upsertProjects($output);

        $this->projectSummaryRenderer->render($output);

        if ($this->dockerComposeManager->up($output)) {
            $output->writeln("=> [<info>INFO</info>] Project configuration is up and running ✔");
            return 0;
        } else {
            $output->writeln("=> [<error>ERROR</error>]  Cannot start docker-compose configuration");
            return 1;
        }*/
    }
}
