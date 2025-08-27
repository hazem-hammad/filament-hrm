<?php

namespace App\Http\Resources\Common;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class PaginatedResourceCollection extends ResourceCollection
{
    protected $collectionName = 'items';

    public function toArray($request)
    {
        $paginator = $this->resource;

        if (! $paginator instanceof LengthAwarePaginator) {
            return [
                $this->collectionName => $this->collection,
                'pagination' => null,
            ];
        }

        return [
            $this->collectionName => $this->collection,
            'pagination' => [
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
            ],
        ];
    }
}
