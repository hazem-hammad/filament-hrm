<?php

namespace App\Repositories;

use App\DTOs\V1\Article\ListArticleDTO;
use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class ArticleRepository implements ArticleRepositoryInterface
{
    public function list(ListArticleDTO $dto): LengthAwarePaginator
    {
        return Article::query()
            ->active()
            ->with('media')
            ->when($dto->getSearch(), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    foreach (config('core.available_locales', ['en']) as $locale) {
                        $q->orWhere("title->{$locale}", 'like', "%{$search}%")
                          ->orWhere("content->{$locale}", 'like', "%{$search}%");
                    }
                });
            })
            ->orderBy($dto->getSortBy(), $dto->getSortOrder())
            ->paginate($dto->getLimit(), ['*'], 'page', $dto->getPage());
    }

    public function findById(int $id): ?Article
    {
        return Article::query()
            ->with('media')
            ->find($id);
    }
}