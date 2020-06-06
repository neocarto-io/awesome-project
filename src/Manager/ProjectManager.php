<?php

namespace AwesomeProject\Manager;

use AwesomeProject\Aggregator\ProjectAggregator;
use AwesomeProject\Model\Configuration\MainConfiguration;
use AwesomeProject\Model\Configuration\ProjectConfiguration;

class ProjectManager
{
    private ProjectAggregator $projectAggregator;

    /** @var ProjectConfiguration[]|null */
    private ?array $projects = null;

    /**
     * @param ProjectAggregator $projectAggregator
     */
    public function __construct(ProjectAggregator $projectAggregator)
    {
        $this->projectAggregator = $projectAggregator;
    }

    /**
     * @return ProjectConfiguration[]
     */
    public function getProjects(): array
    {
        if (is_null($this->projects)) {
            $this->projects = [];
            foreach ($this->projectAggregator->getProjects() as $project) {
                $this->projects[$project->getSlug()] = $project;
            }
        }

        return $this->projects;
    }

    /**
     * @return MainConfiguration
     */
    public function getMainConfiguration(): MainConfiguration
    {
        return $this->projectAggregator->getConfiguration();
    }
}
