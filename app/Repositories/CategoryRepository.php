<?php

namespace App\Repositories;

use App\DTOs\V1\User\Service\ListCategoryDTO;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(ListCategoryDTO $dto): ?LengthAwarePaginator
    {
        return Category::active()->latest('created_at')->filter()
            ->whereHas('services', fn ($q) => $q->active())
            ->paginate($dto->getLimit(), ['*'], 'page', $dto->getPage());
    }
}
