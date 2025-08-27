<?php

namespace App\DTOs\V1\Banner;

use App\DTOs\Common\AbstractDTO;

final class ListBannerDTO extends AbstractDTO
{
    protected function map(array $data): bool
    {
        return true;
    }
}