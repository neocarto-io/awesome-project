<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Command\AbstractCommand;
use AwesomeProject\Helpers\DockerCompose;
use AwesomeProject\Manager\ServicesManager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends AbstractCommand
{
    protected static $defaultName = 'up';

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
        $servicesManager = new ServicesManager();
        $services = $servicesManager->getServices();

        $output->writeln(
            sprintf(
                ' => <info>%s projects discovered: %s</info>' . PHP_EOL,
                count($services),
                implode(', ', array_keys($services))
            )
        );


        $table = new Table($output);

        $table->setHeaders(['name', 'docker-compose', 'php composer']);

        foreach ($services as $serviceConfiguration) {
            $table->addRow(
                [
                    $serviceConfiguration->getSlug(),
                    $serviceConfiguration->getDockerComposeConfig() ? 'yes' : 'no',
                    $serviceConfiguration->getPhpComposerConfig() ? 'yes' : 'no'
                ]
            );
        }

        $table->render();
        $output->writeln('');

        $servicesManager->compile();

//        $upResult = DockerCompose::up($servicesManager->getDockerComposeServicesConfigs(), getcwd());

//        if ($upResult['success']) {
//            $output->writeln(PHP_EOL . ' => <question>Configuration is up!</question>');
//            $output->write($upResult['output']);
//        } else {
//            $output->writeln("<error>ERROR!</error> Something went wrong while trying to start the configuration");
//            $output->write($upResult['error']);
//        }

        return 0;
    }
}
