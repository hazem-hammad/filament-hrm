<?php

namespace App\Filters;

use App\Base\Filters\AbstractFilters;
use App\Filters\Pipelines\CategoryPipeline;

class CategoryFilters extends AbstractFilters
{
    protected function getPipelines(): array
    {
        return [
            new CategoryPipeline($this->request->all()),
        ];
    }
}
