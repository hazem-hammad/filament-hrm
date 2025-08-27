<?php

namespace App\Filters\Pipelines;

use App\Base\Filters\AbstractPipeline;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class IncludePipeline extends AbstractPipeline
{
    public function handle(Builder $builder, Closure $next): Builder // TODO:: ADD select to the include
    {
        if (isset($this->request['include'])) {
            $relations = explode(',', $this->request['include']);
            $builder->with($relations);
        }

        return $next($builder);
    }
}

// TODO:: CLI Command generate rest api with our structure
