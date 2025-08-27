<?php

namespace App\Filters\Pipelines;

use App\Base\Filters\AbstractPipeline;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class WalletTransactionPipeline extends AbstractPipeline
{
    public function handle(Builder $builder, Closure $next): Builder
    {
        $filters = $this->request['filter'] ?? [];
        $builder = $builder
            ->when(isset($filters['type']) && $filters['type'], function (Builder $query) use ($filters) {
                $query->where('type', $filters['type']);
            });

        return $next($builder);
    }
}
