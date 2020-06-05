<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Command\AbstractCommand;

class DownCommand extends AbstractCommand
{
    protected static $defaultName = 'down';

    protected function configure()
    {
        $this->setDescription('Smooth shutdown');
    }
}
