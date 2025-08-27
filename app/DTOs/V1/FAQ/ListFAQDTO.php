<?php

namespace App\DTOs\V1\FAQ;

use App\DTOs\Common\AbstractDTO;

final class ListFAQDTO extends AbstractDTO
{
    protected function map(array $data): bool
    {
        // Uses parent AbstractDTO properties for pagination, search, filters, etc.
        return true;
    }
}