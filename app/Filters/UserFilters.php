<?php

namespace App\Filters;

use App\Base\Filters\AbstractFilters;
use App\Filters\Pipelines\UserPipeline;

class UserFilters extends AbstractFilters
{
    protected function getPipelines(): array
    {
        return [
            new UserPipeline($this->request->all()),
        ];
    }
}
