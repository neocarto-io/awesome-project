<?php

namespace AwesomeProject;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class AwesomeProjectApplication extends Application
{
    public function __construct()
    {
        parent::__construct('AwesomeProject', '0.1.0');
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
