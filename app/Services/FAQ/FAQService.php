<?php

namespace App\Services\FAQ;

use App\DTOs\V1\FAQ\ListFAQDTO;
use App\Models\Faq;
use App\Repositories\Interfaces\FAQRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class FAQService
{
    public function __construct(private readonly FAQRepositoryInterface $repo) {}

    public function list(ListFAQDTO $dto): LengthAwarePaginator
    {
        return $this->repo->list($dto);
    }

    public function findById(int $id): ?Faq
    {
        return $this->repo->findById($id);
    }
}
