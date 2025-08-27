<?php

namespace App\Repositories\Interfaces;

use App\DTOs\V1\Article\ListArticleDTO;
use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function list(ListArticleDTO $dto): LengthAwarePaginator;
    public function findById(int $id): ?Article;
}