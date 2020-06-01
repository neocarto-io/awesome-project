<?php

namespace AwesomeProject;

use AwesomeProject\Command\System\DownCommand;
use AwesomeProject\Command\System\KillCommand;
use AwesomeProject\Command\System\RestartCommand;
use AwesomeProject\Command\System\UpCommand;
use AwesomeProject\Model\ServiceConfiguration;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

class AwesomeProjectApplication extends Application
{
    public function __construct()
    {
        parent::__construct('AwesomeProject', '0.1.0');

        $this->add(new UpCommand());
        $this->add(new DownCommand());
        $this->add(new RestartCommand());
        $this->add(new KillCommand());
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $output = $output ?? new ConsoleOutput();

        try {
            parent::run($input, $output);
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s:</error> %s', get_class($exception), $exception->getMessage()));
        }
    }
}