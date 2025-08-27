<?php

namespace App\Filters;

use App\Base\Filters\AbstractFilters;
use App\Filters\Pipelines\PropertyPipeline;

class PropertyFilters extends AbstractFilters
{
    protected function getPipelines(): array
    {
        return [
            new PropertyPipeline($this->request->all()),
        ];
    }
}
