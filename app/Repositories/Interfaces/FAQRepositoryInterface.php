<?php

namespace App\Repositories\Interfaces;

use App\DTOs\V1\FAQ\ListFAQDTO;
use App\Models\Faq;
use Illuminate\Pagination\LengthAwarePaginator;

interface FAQRepositoryInterface
{
    public function list(ListFAQDTO $dto): LengthAwarePaginator;
    public function findById(int $id): ?Faq;
}
