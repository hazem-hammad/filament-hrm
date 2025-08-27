<?php

namespace App\Filters\Pipelines;

use App\Base\Filters\AbstractPipeline;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class CategoryPipeline extends AbstractPipeline
{
    public function handle(Builder $builder, Closure $next): Builder
    {
        $search = $this->request['search'] ?? null;
        $filters = $this->request['filter'] ?? [];

        $builder = $builder
            ->when(isset($filters['expert_id']), function (Builder $query) use ($filters) {
                $query->whereHas('services.expertServices', function ($q) use ($filters) {
                    $q->where('user_id', $filters['expert_id']);
                })->with(['services' => function ($serviceQuery) use ($filters) {
                    $serviceQuery->active()
                        ->whereHas('expertServices', function ($q) use ($filters) {
                            $q->where('user_id', $filters['expert_id']);
                        })
                        ->with(['expertServices' => function ($q) use ($filters) {
                            $q->where('user_id', $filters['expert_id'])
                                ->select('expert_services.id', 'service_id', 'user_id', 'expert_services.bio', 'expert_services.hour_rate');
                        }]);
                }]);
            })->when(! isset($filters['expert_id']), function (Builder $query) {
                $query->with(['services' => fn ($q) => $q->active()]);
            })

            ->when($search, function (Builder $query) use ($search) {
                $locales = config('core.available_locales');
                $searchTerm = "%{$search}%";

                $query->where(function ($q) use ($locales, $searchTerm) {
                    foreach ($locales as $locale) {
                        applyLocalizedSearch($q, $locales, $searchTerm, ['name']);
                    }
                })
                    ->orWhereHas('services', function ($serviceQuery) use ($locales, $searchTerm) {
                        $serviceQuery->active()
                            ->where(function ($sq) use ($locales, $searchTerm) {
                                applyLocalizedSearch($sq, $locales, $searchTerm, ['name', 'description']);
                            });
                    });
            })->when(
                isset($filters['exclude_services']) && $filters['exclude_services'] &&
                auth('api')->check(), function (Builder $query) {
                    $userId = auth('api')->id();

                    $query->whereHas('services', function ($serviceQuery) use ($userId) {
                        $serviceQuery->active()
                            ->whereDoesntHave('expertServices', function ($q) use ($userId) {
                                $q->where('user_id', $userId);
                            });
                    })
                        ->with(['services' => function ($serviceQuery) use ($userId) {
                            $serviceQuery->active()
                                ->whereDoesntHave('expertServices', function ($q) use ($userId) {
                                    $q->where('user_id', $userId);
                                });
                        }]);
                }
            );

        return $next($builder);
    }
}
