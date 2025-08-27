<?php

namespace App\Filters\Pipelines;

use App\Base\Filters\AbstractPipeline;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SearchPipeline extends AbstractPipeline
{
    public function __construct(array $request, protected Model $model)
    {
        parent::__construct($request);
    }

    public function handle(Builder $builder, Closure $next): Builder
    {
        if (isset($this->request['search'])) {

            $searchTerm = '%'.trim($this->request['search']).'%';
            $searchFields = $this->model->getAllowedSearchFields() ?? [];

            $builder->where(function ($query) use ($searchTerm, $searchFields) {
                foreach ($searchFields as $field) {
                    $query->orWhere($field, 'LIKE', $searchTerm);
                }
            });
        }

        return $next($builder);
    }
}
