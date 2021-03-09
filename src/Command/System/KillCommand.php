<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Command\AbstractCommand;
use AwesomeProject\Traits\DockerComposeAwareTrait;
use AwesomeProject\Traits\ProjectSummaryRendererTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KillCommand extends AbstractCommand
{
    use ProjectSummaryRendererTrait, DockerComposeAwareTrait;

    protected static $defaultName = 'kill';


    protected function configure()
    {
        $this->setDescription('Kill all processes as fast as possible');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        if ($this->dockerComposeManager->kill($output)) {
            $output->writeln("=> [<info>INFO</info>] All services were killed");
            return 0;
        } else {
            $output->writeln("=> [<error>ERROR</error>] Cannot kill docker-compose configuration");
            return 1;
        }
    }
}
