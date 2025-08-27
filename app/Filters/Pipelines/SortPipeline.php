<?php

namespace App\Filters\Pipelines;

use App\Base\Filters\AbstractPipeline;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SortPipeline extends AbstractPipeline
{
    public const ASC = 'asc';

    public const DESC = 'desc';

    public function __construct(array $request, protected Model $model)
    {
        parent::__construct($request);
    }

    public function handle(Builder $builder, Closure $next): Builder
    {
        $sorts = $this->request['sort'] ?? [];

        foreach ($sorts as $column => $direction) {

            $direction = in_array(strtolower($direction), [self::ASC, self::DESC])
                ? strtolower($direction)
                : self::DESC;

            $builder->orderBy($column, $direction);
        }

        if (empty($sorts)) {
            $builder->orderBy('created_at', 'desc');
        }

        return $next($builder);
    }
}
