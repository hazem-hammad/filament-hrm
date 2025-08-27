<?php

namespace App\Filters;

use App\Base\Filters\AbstractFilters;
use App\Filters\Pipelines\ProjectPipeline;

class ProjectFilters extends AbstractFilters
{
    protected function getPipelines(): array
    {
        return [
            new ProjectPipeline($this->request->all()),
        ];
    }
}
