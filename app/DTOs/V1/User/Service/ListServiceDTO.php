<?php

namespace App\DTOs\V1\User\Service;

use App\DTOs\Common\AbstractDTO;

class ListServiceDTO extends AbstractDTO
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
