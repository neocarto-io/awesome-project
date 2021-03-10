<?php

declare(strict_types=1);

namespace AwesomeProject\Manager;

use AwesomeProject\Aggregator\DockerComposeAggregator;
use AwesomeProject\Aggregator\HttpGatewayAggregator;
use AwesomeProject\Traits\ProcessControlTrait;
use JMS\Serializer\Serializer;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DockerComposeManager
{
    use ProcessControlTrait;

    /**
     * @param DockerComposeAggregator $dockerComposeAggregator
     * @param HttpGatewayAggregator $httpGatewayAggregator
     * @param Serializer $serializer
     */
    public function __construct(
        private DockerComposeAggregator $dockerComposeAggregator,
        private HttpGatewayAggregator $httpGatewayAggregator,
        private Serializer $serializer
    ) {
    }

    /**
     * Compile aggregated configuration
     */
    public function compileConfiguration()
    {
        $dockerComposeProject = $this->dockerComposeAggregator->getAggregatedProject();

        $this->httpGatewayAggregator->attachHttpGatewayService($dockerComposeProject);

        file_put_contents(
            getcwd() . "/docker-compose.yaml",
            Yaml::dump($this->serializer->toArray($dockerComposeProject), 4, 2, Yaml::DUMP_NULL_AS_TILDE)
        );
    }

    /**
     * Attempt to start all services
     *
     * @param OutputInterface|null $output
     * @return bool
     */
    public function up(?OutputInterface $output = null): bool
    {
        return $this->execute(['docker-compose', 'up', '-d', '--remove-orphans'], ['output' => $output]);
    }

    /**
     * Attempt to kill all services
     *
     * @param OutputInterface|null $output
     * @return bool
     */
    public function kill(?OutputInterface $output = null): bool
    {
        return $this->execute(['docker-compose', 'kill'], ['output' => $output]);
    }

    /**
     * Attempt to stop all services
     *
     * @param OutputInterface|null $output
     * @return bool
     */
    public function down(?OutputInterface $output = null): bool
    {
        return $this->execute(['docker-compose', 'down'], ['output' => $output]);
    }

    /**
     * Attempt to restart all services
     *
     * @param OutputInterface|null $output
     * @return bool
     */
    public function restart(?OutputInterface $output = null): bool
    {
        return $this->execute(['docker-compose', 'restart'], ['output' => $output]);
    }
}
