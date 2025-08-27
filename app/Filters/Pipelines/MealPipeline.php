<?php

namespace App\Filters\Pipelines;

use App\Base\Filters\AbstractPipeline;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class MealPipeline extends AbstractPipeline
{
    public function handle(Builder $builder, Closure $next): Builder
    {
        $filters = $this->request['filter'] ?? [];
        $builder = $builder
            ->when(isset($filters['restaurant_id']), function ($query) use ($filters) {
                $query->where('restaurant_id', $filters['restaurant_id']);
            })
            ->when(isset($filters['category_id']), function ($query) use ($filters) {
                $query->whereHas('categories', function ($query) use ($filters) {
                    $query->where('categories.id', $filters['category_id']);
                });
            });

        return $next($builder);
    }
}
