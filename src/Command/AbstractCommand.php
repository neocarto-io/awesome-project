<?php

namespace AwesomeProject\Command;

use AwesomeProject\Model\ServiceConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

abstract class AbstractCommand extends Command
{
    /**
     * @var ServiceConfiguration[]
     */
    protected array $services;




}