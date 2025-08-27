<?php

namespace App\Filters;

use App\Base\Filters\AbstractFilters;
use App\Filters\Pipelines\ServicePipeline;

class ServiceFilters extends AbstractFilters
{
    protected function getPipelines(): array
    {
        return [
            new ServicePipeline($this->request->all()),
        ];
    }
}
