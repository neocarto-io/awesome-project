<?php

declare(strict_types=1);

namespace AwesomeProject\Model;

class RootProject
{
    /** @var array|Project[] */
    private array $projects;

    /**
     * @param Project $project
     * @return $this
     */
    public function addProject(Project $project): self
    {
        $this->projects[$project->getName()] = $project;
        return $this;
    }

    /**
     * @return array
     */
    public function getProjectNames(): array
    {
        return array_keys($this->projects);
    }

    /**
     * @param string $projectName
     * @return Project|null
     */
    public function getProject(string $projectName): ?Project
    {
        return $this->projects[$projectName] ?? null;
    }

    /**
     * @return array|Project[]
     */
    public function getProjects(): array
    {
        return $this->projects;
    }
}
