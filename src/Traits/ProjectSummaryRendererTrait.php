<?php

declare(strict_types=1);

namespace AwesomeProject\Traits;

use AwesomeProject\Console\Renderer\ProjectSummaryRenderer;

trait ProjectSummaryRendererTrait
{
    protected ProjectSummaryRenderer $projectSummaryRenderer;

    /**
     * @param ProjectSummaryRenderer $projectSummaryRenderer
     * @return $this
     */
    public function setProjectSummaryRenderer(ProjectSummaryRenderer $projectSummaryRenderer): self
    {
        $this->projectSummaryRenderer = $projectSummaryRenderer;
        return $this;
    }
}
