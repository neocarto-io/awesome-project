<?php

namespace AwesomeProject\Helpers;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

abstract class DockerCompose
{
    /**
     * @param string $baseDir
     * @return \Iterator
     * @throws DirectoryNotFoundException in case baseDir is invalid
     */
    public static function getConfigurationFiles(string $baseDir): \Iterator
    {
        $finder = (new Finder())
            ->in($baseDir)
            ->name("*docker-compose.yaml");

        foreach ($finder->files() as $fileInfo) {
            $path = realpath($fileInfo->getPathname());
            yield [
                'configPath' => $path,
                'projectName' => basename($fileInfo->getPath()),
                'projectPath' => $fileInfo->getPath()
            ];
        }
    }

    /**
     * `docker-compose up -d` with given configuration files
     *
     * @param array $configurationFiles
     * @param string|null $workingDirectory
     * @return array
     */
    public static function up(array $configurationFiles, ?string $workingDirectory): array
    {
        $command = ['docker-compose'];

        foreach ($configurationFiles as $configurationFile) {
            array_push($command, ...['-f', $configurationFile]);
        }

        array_push($command, ...['up', '-d']);

        $process = new Process($command, $workingDirectory);

        $process->run();

        return [
            'success' => $process->isSuccessful(),
            'error' => $process->getErrorOutput(),
            'output' => $process->getOutput()
        ];
    }
}