<?php

namespace App\Services\Article;

use App\DTOs\V1\Article\ListArticleDTO;
use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class ArticleService
{
    public function __construct(private readonly ArticleRepositoryInterface $repo) {}

    public function list(ListArticleDTO $dto): LengthAwarePaginator
    {
        return $this->repo->list($dto);
    }

    public function findById(int $id): ?Article
    {
        return $this->repo->findById($id);
    }
}