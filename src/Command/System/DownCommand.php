<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Command\AbstractCommand;
use AwesomeProject\Traits\DockerComposeAwareTrait;
use AwesomeProject\Traits\ProjectSummaryRendererTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownCommand extends AbstractCommand
{
    use DockerComposeAwareTrait, ProjectSummaryRendererTrait;

    protected static $defaultName = 'down';

    protected function configure()
    {
        $this->setDescription('Smooth shutdown');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        if ($this->dockerComposeManager->down($output)) {
            $output->writeln("=> [<info>INFO</info>] All services were stopped ✔");
            return 0;
        } else {
            $output->writeln("=> [<error>ERROR</error>]  Cannot stop docker-compose configuration");
            return 1;
        }
    }
}
