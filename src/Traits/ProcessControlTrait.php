<?php

declare(strict_types=1);

namespace AwesomeProject\Traits;

use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

trait ProcessControlTrait
{
    public function execute(array $command, array $options = []): bool
    {
        $output = $options['output'] ?? new NullOutput();

        $output = $output ?? new NullOutput();

        $process = new Process(
            $command,
            $options['workingDirectory'] ?? getcwd(),
            $options['env'] ?? null,
            $options['input'] ?? null,
            $options['timeout'] ?? 0
        );

        $helperSet = new HelperSet([new ProcessHelper(), new DebugFormatterHelper()]);

        $helper = $helperSet->get('process');

        $helper->run($output, $process);
        $output->write(PHP_EOL);

        return $process->isSuccessful();
    }
}
