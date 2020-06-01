<?php

declare(strict_types=1);

namespace AwesomeProject\Command\System;

use AwesomeProject\Command\AbstractCommand;

class RestartCommand extends AbstractCommand
{
    protected static $defaultName = 'restart';

    protected function configure()
    {
        $this->setDescription('Recompule/Restart the cofiguration');
    }

}