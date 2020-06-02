<?php

declare(strict_types=1);

namespace AwesomeProject\DependencyInjection\CompilerPass;

use AwesomeProject\AwesomeProjectApplication;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CommandCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $commands = $container->findTaggedServiceIds('command');
        $cliApp = $container->getDefinition(AwesomeProjectApplication::class);

        foreach ($commands as $serviceId => $command) {
            $cliApp->addMethodCall('add', [new Reference($serviceId)]);
        }
    }
}
