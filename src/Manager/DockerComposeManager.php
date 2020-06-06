<?php

declare(strict_types=1);

namespace AwesomeProject\Manager;

use AwesomeProject\Aggregator\DockerComposeAggregator;
use AwesomeProject\Aggregator\HttpGatewayAggregator;
use JMS\Serializer\Serializer;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class DockerComposeManager
{
    private ProjectManager $projectManager;
    private DockerComposeAggregator $dockerComposeAggregator;
    private HttpGatewayAggregator $httpGatewayAggregator;
    private Serializer $serializer;

    /**
     * @param ProjectManager $projectManager
     * @param DockerComposeAggregator $dockerComposeAggregator
     * @param HttpGatewayAggregator $httpGatewayAggregator
     * @param Serializer $serializer
     */
    public function __construct(
        ProjectManager $projectManager,
        DockerComposeAggregator $dockerComposeAggregator,
        HttpGatewayAggregator $httpGatewayAggregator,
        Serializer $serializer
    )
    {
        $this->projectManager = $projectManager;
        $this->dockerComposeAggregator = $dockerComposeAggregator;
        $this->httpGatewayAggregator = $httpGatewayAggregator;
        $this->serializer = $serializer;
    }

    /**
     * Compile aggregated configuration
     */
    public function compileConfiguration()
    {
        $dockerComposeConfiguration = $this->dockerComposeAggregator->aggregateConfiguration(
            $this->projectManager->getProjects()
        );

        $this->httpGatewayAggregator->attachHttpGatewayService($dockerComposeConfiguration);

        file_put_contents(
            getcwd() . "/docker-compose.yaml",
            Yaml::dump($this->serializer->toArray($dockerComposeConfiguration), 4, 2, Yaml::DUMP_NULL_AS_TILDE)
        );
    }

    /**
     * Attempt to start all services
     * @return PromiseInterface
     */
    public function up(): PromiseInterface
    {
        $deferred = new Deferred();

        $process = new Process(['docker-compose', 'up', '-d'], getcwd(), null, null, 0);

        $process->start();

        while (!$process->isTerminated()) {
            echo $process->getIncrementalOutput();
        }

        if ($process->isSuccessful()) {
            $deferred->resolve();
        } else {
            $deferred->reject(new \RuntimeException($process->getErrorOutput()));
        }

        return $deferred->promise();
    }
}