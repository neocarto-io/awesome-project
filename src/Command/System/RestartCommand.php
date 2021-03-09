<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Command\AbstractCommand;
use AwesomeProject\Traits\DockerComposeAwareTrait;
use AwesomeProject\Traits\ProjectSummaryRendererTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RestartCommand extends AbstractCommand
{
    use DockerComposeAwareTrait, ProjectSummaryRendererTrait;

    protected static $defaultName = 'restart';

    protected function configure()
    {
        $this->setDescription('Recompule/Restart the cofiguration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->projectSummaryRenderer->render($output);

        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        if ($this->dockerComposeManager->restart($output)) {
            $output->writeln("=> [<info>INFO</info>] Project configuration is up and running ✔");
            return 0;
        } else {
            $output->writeln("=> [<error>ERROR</error>]  Cannot re-start docker-compose configuration");
            return 1;
        }
    }
}
