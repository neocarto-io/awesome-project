<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Command\AbstractCommand;
use AwesomeProject\Traits\DockerComposeAwareTrait;
use AwesomeProject\Traits\ProjectSummaryRendererTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends AbstractCommand
{
    use ProjectSummaryRendererTrait, DockerComposeAwareTrait;

    protected static $defaultName = 'up';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Start the configuration');
        $this->addOption('update', 'u', InputOption::VALUE_NONE, 'Update repositories before compiling');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->projectSummaryRenderer->render($output);

        $this->dockerComposeManager->compileConfiguration();

        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $this->dockerComposeManager->kill(['awesome-http-gateway'], $output);

        if ($this->dockerComposeManager->up($output)) {
            $output->writeln("=> [<info>INFO</info>] Project configuration is up and running ✔");
            return 0;
        } else {
            $output->writeln("=> [<error>ERROR</error>]  Cannot start docker-compose configuration");
            return 1;
        }
    }
}
