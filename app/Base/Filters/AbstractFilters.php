<?php

namespace App\Base\Filters;

use App\Filters\Pipelines\FilterPipeline;
use App\Filters\Pipelines\IncludePipeline;
use App\Filters\Pipelines\SearchPipeline;
use App\Filters\Pipelines\SortPipeline;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

abstract class AbstractFilters
{
    protected array $filters = [];

    public function __construct(protected Request $request, protected Builder $builder) {}

    public function apply(): Builder
    {
        return app(Pipeline::class)
            ->send($this->builder)
            ->through(array_merge($this->defaultPipelines(), $this->getPipelines()))
            ->thenReturn();
    }

    abstract protected function getPipelines(): array;

    private function defaultPipelines(): array
    {
        return [
            new SearchPipeline($this->request->all(), $this->builder->getModel()),
            new SortPipeline($this->request->all(), $this->builder->getModel()),
            new FilterPipeline($this->request->all()),
            new IncludePipeline($this->request->all()),
        ];

    }
}
