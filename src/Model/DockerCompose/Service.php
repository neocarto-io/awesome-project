<?php

declare(strict_types=1);

namespace AwesomeProject\Model\DockerCompose;

use JMS\Serializer\Annotation as Serializer;

class Service
{
    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private ?string $image = null;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private ?string $command = null;

    /**
     * @var EnvironmentVariable[]|null
     * @Serializer\Type("array<EnvironmentVariableString>")
     */
    private ?array $environment = null;

    /**
     * @var string[]|null
     * @Serializer\Type("array<string>")
     */
    private ?array $envFile = null;

    /**
     * @var Volume[]|null
     * @Serializer\Type("array<VolumeString>")
     */
    private ?array $volumes = null;

    /**
     * @var string[]|null
     * @Serializer\Type("array<string>")
     */
    private ?array $links = null;

    /**
     * @var string[]|null
     * @Serializer\Type("array<string>")
     */
    private ?array $networks = null;

    /**
     * @var string[]|null
     * @Serializer\Type("array<PortMappingString>")
     */
    private ?array $ports = null;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private ?string $build = null;
    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private ?string $workingDir = null;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private ?string $hostname = null;

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @return string|null
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * @return EnvironmentVariable[]|null
     */
    public function getEnvironment(): ?array
    {
        return $this->environment;
    }

    /**
     * @return string[]|null
     */
    public function getEnvFile(): ?array
    {
        return $this->envFile;
    }

    /**
     * @return Volume[]|null
     */
    public function getVolumes(): ?array
    {
        return $this->volumes;
    }

    /**
     * @return string[]|null
     */
    public function getLinks(): ?array
    {
        return $this->links;
    }

    /**
     * @return string[]|null
     */
    public function getNetworks(): ?array
    {
        return $this->networks;
    }

    /**
     * @return string[]|null
     */
    public function getPorts(): ?array
    {
        return $this->ports;
    }

    /**
     * @return string|null
     */
    public function getBuild(): ?string
    {
        return $this->build;
    }

    /**
     * @return string|null
     */
    public function getWorkingDir(): ?string
    {
        return $this->workingDir;
    }

    /**
     * @param string|null $image
     * @return $this
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @param string|null $command
     * @return $this
     */
    public function setCommand(?string $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @param EnvironmentVariable[]|null $environment
     * @return $this
     */
    public function setEnvironment(?array $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @param string[]|null $envFile
     * @return $this
     */
    public function setEnvFile(?array $envFile): self
    {
        $this->envFile = $envFile;

        return $this;
    }

    /**
     * @param Volume[]|null $volumes
     * @return $this
     */
    public function setVolumes(?array $volumes): self
    {
        $this->volumes = $volumes;

        return $this;
    }

    /**
     * @param string[]|null $links
     * @return $this
     */
    public function setLinks(?array $links): self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @param string $link
     * @return $this
     */
    public function addLink(string $link): self
    {
        if (in_array($link, $this->links)) {
            return $this;
        }

        $this->links[] = $link;

        return $this;
    }

    /**
     * @param string[]|null $networks
     * @return $this
     */
    public function setNetworks(?array $networks): self
    {
        $this->networks = $networks;

        return $this;
    }

    /**
     * @param string $network
     * @return $this
     */
    public function addNetwork(string $network): self
    {
        if (in_array($network, $this->networks)) {
            return $this;
        }
        $this->networks[] = $network;

        return $this;
    }

    /**
     * @param string[]|null $ports
     * @return $this
     */
    public function setPorts(?array $ports): self
    {
        $this->ports = $ports;

        return $this;
    }

    /**
     * @param string|null $build
     * @return $this
     */
    public function setBuild(?string $build): self
    {
        $this->build = $build;

        return $this;
    }

    /**
     * @param string|null $workingDir
     * @return $this
     */
    public function setWorkingDir(?string $workingDir): self
    {
        $this->workingDir = $workingDir;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    /**
     * @param string|null $hostname
     * @return $this
     */
    public function setHostname(?string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getDeploy(): ?array
    {
        return $this->deploy;
    }

    /**
     * @param array|null $deploy
     * @return $this
     */
    public function setDeploy(?array $deploy): self
    {
        $this->deploy = $deploy;
        return $this;
    }
}
