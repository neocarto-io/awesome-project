<?php

declare(strict_types=1);

namespace AwesomeProject\Serializer;

use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder as BaseSerializerBuilder;

class SerializerBuilder
{
    /**
     * @return Serializer
     */
    public static function create(): Serializer
    {
        return BaseSerializerBuilder::create()
            ->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new DockerComposeSerializerHandler());
            })
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->build();
    }
}
