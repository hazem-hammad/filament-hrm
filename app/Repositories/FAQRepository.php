<?php

namespace App\Repositories;

use App\DTOs\V1\FAQ\ListFAQDTO;
use App\Models\Faq;
use App\Repositories\Interfaces\FAQRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class FAQRepository implements FAQRepositoryInterface
{
    public function list(ListFAQDTO $dto): LengthAwarePaginator
    {
        return Faq::query()
            ->active()
            ->when($dto->getSearch(), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    foreach (config('core.available_locales', ['en']) as $locale) {
                        $q->orWhere("question->{$locale}", 'like', "%{$search}%")
                            ->orWhere("answer->{$locale}", 'like', "%{$search}%");
                    }
                });
            })
            ->orderBy($dto->getSortBy(), $dto->getSortOrder())
            ->paginate($dto->getLimit(), ['*'], 'page', $dto->getPage());
    }

    public function findById(int $id): ?Faq
    {
        return Faq::query()->find($id);
    }
}
