<?php

namespace App\Filters\Pipelines;

use App\Base\Filters\AbstractPipeline;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterPipeline extends AbstractPipeline
{
    public function handle(Builder $builder, Closure $next): Builder
    {

        return $next($builder);
    }
}
