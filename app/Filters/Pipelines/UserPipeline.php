<?php

namespace App\Filters\Pipelines;

use App\Base\Filters\AbstractPipeline;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class UserPipeline extends AbstractPipeline
{
    public function handle(Builder $builder, Closure $next): Builder
    {
        $filters = $this->request['filter'] ?? [];

        $builder = $builder
            // price filter
            ->when(isset($filters['min_price']) && isset($filters['max_price']), function ($query) use ($filters) {
                $query->whereHas('expertServices', function ($q) use ($filters) {
                    $q->whereBetween('expert_services.hour_rate', [$filters['min_price'], $filters['max_price']])
                        ->active(); // scope on Service model
                });
            })
            ->when(isset($filters['min_price']) && ! isset($filters['max_price']), function ($query) use ($filters) {
                $query->whereHas('expertServices', function ($q) use ($filters) {
                    $q->where('expert_services.hour_rate', '>=', $filters['min_price'])
                        ->active();
                });
            })
            ->when(isset($filters['max_price']) && ! isset($filters['min_price']), function ($query) use ($filters) {
                $query->whereHas('expertServices', function ($q) use ($filters) {
                    $q->where('expert_services.hour_rate', '<=', $filters['max_price'])
                        ->where('services.status', true);
                });
            })
            // service filter
            ->when(isset($filters['service_id']) && is_array($filters['service_id']), function ($query) use ($filters) {
                $query->whereHas('expertServices', function ($q) use ($filters) {
                    $q->active()
                        ->whereIn('services.id', $filters['service_id']);
                });
            })
            // category filter
            ->when(isset($filters['category_id']) && is_array($filters['category_id']), function ($query) use ($filters) {
                $query->whereHas('expertServices.category', function ($categoryQuery) use ($filters) {
                    $categoryQuery->active()
                        ->whereIn('categories.id', $filters['category_id']);
                });
            })
            // featured filter
            ->when(isset($filters['is_featured']), function ($query) use ($filters) {
                $query->where('is_featured', booleanValue($filters['is_featured']));
            })
            // favorite users filters
            ->when(isset($filters['is_favorite']) && auth('api')->check() && $filters['is_favorite'], function ($query) {
                $query->whereHas('favorites', function ($q) {
                    $q->where('favoritable_type', User::class)
                        ->where('user_id', auth('api')->id());
                });
            });

        // TODO: Add rate filter
        return $next($builder);
    }
}
