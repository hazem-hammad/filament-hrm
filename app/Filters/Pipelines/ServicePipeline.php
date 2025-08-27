<?php

namespace App\Filters\Pipelines;

use App\Base\Filters\AbstractPipeline;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class ServicePipeline extends AbstractPipeline
{
    public function handle(Builder $builder, Closure $next): Builder
    {
        $filters = $this->request['filter'] ?? [];
        $search = $this->request['search'] ?? null;
        $builder = $builder
            // is featured service
            ->when(isset($filters['is_featured']) && $filters['is_featured'], function (Builder $query) use ($filters) {
                $query->where('is_featured', $filters['is_featured']);
            })
            // category id filter
            ->when(isset($filters['category_id']), function (Builder $query) use ($filters) {
                $query->where('category_id', $filters['category_id']);
            })
            // search
            ->when($search, function (Builder $query) use ($search) {
                $locales = config('core.available_locales');
                $searchTerm = "%{$search}%";
                $query->where(function ($q) use ($locales, $searchTerm) {
                    applyLocalizedSearch($q, $locales, $searchTerm, ['name', 'description']);
                });
            })
            // expert id filter
            ->when(isset($filters['expert_id']), function (Builder $query) use ($filters) {
                $query->whereHas('expertServices', function ($q) use ($filters) {
                    $q->where('users.id', $filters['expert_id']);
                });
            });

        return $next($builder);
    }
}
