<?php

declare(strict_types=1);

namespace AwesomeProject\Serializer;

use AwesomeProject\Model\DockerCompose\EnvironmentVariable;
use AwesomeProject\Model\DockerCompose\PortMapping;
use AwesomeProject\Model\DockerCompose\Volume;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class DockerComposeSerializerHandler implements SubscribingHandlerInterface
{
    /**
     * @return array|array[]
     */
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'EnvironmentVariableString',
                'method' => 'deserializeEnvironmentVariableString'
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'EnvironmentVariableString',
                'method' => 'serializeEnvironmentVariable'
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'VolumeString',
                'method' => 'deserializeVolumeString'
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'VolumeString',
                'method' => 'serializeVolume'
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'PortMappingString',
                'method' => 'deserializePortMapping'
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'PortMappingString',
                'method' => 'serializePortMapping'
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'Networks',
                'method' => 'passThroughNetworks'
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'Networks',
                'method' => 'passThroughNetworks'
            ],
        ];
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param string $environmentVariableString
     * @param array $type
     * @param Context $context
     * @return EnvironmentVariable
     */
    public function deserializeEnvironmentVariableString(
        JsonDeserializationVisitor $visitor,
        string $environmentVariableString,
        array $type,
        Context $context
    ) {
        list($key, $value) = explode('=', $environmentVariableString, 2);

        return new EnvironmentVariable($key, $value);
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param EnvironmentVariable $environmentVariable
     * @param array $type
     * @param Context $context
     * @return string
     */
    public function serializeEnvironmentVariable(
        JsonSerializationVisitor $visitor,
        EnvironmentVariable $environmentVariable,
        array $type,
        Context $context
    ) {
        return (string)$environmentVariable;
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param string $portMappingString
     * @param array $type
     * @param Context $context
     * @return PortMapping
     */
    public function deserializePortMapping(
        JsonDeserializationVisitor $visitor,
        string $portMappingString,
        array $type,
        Context $context
    ) {
        list($host, $container) = explode(':', $portMappingString, 2);

        return new PortMapping($host, $container);
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param PortMapping $portMapping
     * @param array $type
     * @param Context $context
     * @return string
     */
    public function serializePortMapping(
        JsonSerializationVisitor $visitor,
        PortMapping $portMapping,
        array $type,
        Context $context
    ) {
        return (string)$portMapping;
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param Volume $volume
     * @param array $type
     * @param Context $context
     * @return string
     */
    public function serializeVolume(JsonSerializationVisitor $visitor, Volume $volume, array $type, Context $context)
    {
        return (string)$volume;
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param string $environmentVariableString
     * @param array $type
     * @param Context $context
     * @return Volume
     */
    public function deserializeVolumeString(
        JsonDeserializationVisitor $visitor,
        string $environmentVariableString,
        array $type,
        Context $context
    ) {
        list($key, $value) = explode(':', $environmentVariableString, 2);

        return new Volume($key, $value);
    }

    /**
     * @param JsonSerializationVisitor|JsonDeserializationVisitor $visitor
     * @param array $networks
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function passThroughNetworks($visitor, array $networks, array $type, Context $context)
    {
        return $networks;
    }
}
