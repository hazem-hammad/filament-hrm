<?php

namespace App\DTOs\V1\User\Expert;

use App\DTOs\Common\AbstractDTO;

class ListExpertDTO extends AbstractDTO
{
    public function toArray(): array
    {
        return [];
    }

    protected function map(array $data): bool
    {

        return true;
    }
}
