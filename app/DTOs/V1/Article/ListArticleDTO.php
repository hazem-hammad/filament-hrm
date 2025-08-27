<?php

namespace App\DTOs\V1\Article;

use App\DTOs\Common\AbstractDTO;

final class ListArticleDTO extends AbstractDTO
{
    protected function map(array $data): bool
    {
        // Uses parent AbstractDTO properties for pagination, search, filters, etc.
        return true;
    }
}