#!/usr/bin/env php
<?php

namespace AwesomeProject;

use AwesomeProject\DependencyInjection\CompilerPass\CommandCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\VarDumper\VarDumper;

require_once 'vendor/autoload.php';

VarDumper::setHandler();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addCompilerPass(new CommandCompilerPass());

$loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
$loader->load('src/Resources/config/services.yaml');

$containerBuilder->compile();

$app = $containerBuilder->get(AwesomeProjectApplication::class);

$app->run();
