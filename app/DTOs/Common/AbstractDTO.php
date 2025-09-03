<?php

namespace App\DTOs\Common;

use InvalidArgumentException;

abstract class AbstractDTO implements DtoInterface
{
    protected ?string $search = null;

    protected string $sortBy = 'created_at';

    protected string $sortOrder = 'desc';

    protected bool $paginated = false;

    protected ?int $limit = null;

    protected ?int $page = null;

    protected ?array $include = null;

    protected array $filters = [];

    public function __construct(array $data = [])
    {
        if (! $this->map($data)) {
            throw new InvalidArgumentException('There\'e are missing data please check your request');
        }

        $this->setupFilters($data);
        $this->setupIncludes($data);
    }

    abstract protected function map(array $data): bool;

    private function setupIncludes(array $data): void
    {
        $this->include = isset($data['include']) ? $this->extractLazyLoadObjects($data['include']) : null;
    }

    private function extractLazyLoadObjects(string $include): array
    {
        return array_filter(explode(',', $include));
    }

    private function setupFilters(array $data): void
    {

        $this->paginated = $data['paginated'] ?? false;
        $this->limit = $data['limit'] ?? null;
        $this->page = $data['page'] ?? null;
        $this->filters = $data['filters'] ?? [];
        $this->search = $data['search'] ?? null;
        if (isset($data['sort_by'])) {
            $this->sortBy = $data['sort_by'];
        }
        if (isset($data['sort_order'])) {
            $this->sortOrder = strtolower($data['sort_order']) === 'asc' ? 'asc' : 'desc';
        }
    }

    public function getPage(): ?int
    {
        return $this->page ?? 1;
    }

    public function getLimit(): ?int
    {
        return $this->limit ?? config('core.pagination.default_limit');
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    public function isPaginated(): bool
    {
        return $this->paginated;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getInclude(): ?array
    {
        return $this->include;
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'sort_by' => $this->sortBy,
            'sort_order' => $this->sortOrder,
            'paginated' => $this->paginated,
            'limit' => $this->limit,
            'page' => $this->page,
            'filters' => $this->filters,
            'include' => $this->include,
        ];
    }
}
