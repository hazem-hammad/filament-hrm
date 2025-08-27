<?php

namespace App\Base\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class AbstractPipeline
{
    protected array $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    abstract public function handle(Builder $builder, Closure $next): Builder;
}
