<?php

declare(strict_types=1);

namespace AwesomeProject\Manager;

use Symfony\Component\Process\Process;

class GitManager
{
    /**
     * @param string $path
     * @return string
     */
    public function getBranch(string $path)
    {
        $process = new Process(['git', 'rev-parse', '--symbolic-full-name', '--abbrev-ref', 'HEAD'], $path);

        $process->run();

        return trim($process->getOutput());
    }

    /**
     * @param string $path
     * @return string
     */
    public function remoteUpdate(string $path)
    {
        $process = new Process(['git', 'remote', 'update'], $path);

        $process->run();

        return trim($process->getOutput());
    }

    /**
     * @param string $path
     * @return string
     */
    public function getOrigin(string $path)
    {
        $process = new Process(['git', 'remote', 'get-url', 'origin'], $path);

        $process->run();

        return trim($process->getOutput());
    }

    /**
     * @param string $path
     * @return string
     */
    public function getState(string $path)
    {
        $process = new Process(
            [
                'git',
                'for-each-ref',
                '--format=%(refname:short) %(upstream:trackshort) %(upstream:remotename)',
                'refs/heads/' . $this->getBranch($path)
            ], $path
        );

        $process->run();

        return trim($process->getOutput());
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isGit(string $path): bool
    {
        return is_dir("$path/.git");
    }
}
