<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Command\AbstractCommand;

class KillCommand extends AbstractCommand
{
    protected static $defaultName = 'kill';

    protected function configure()
    {
        $this->setDescription('Kill all processes as fast as possible');
    }
}