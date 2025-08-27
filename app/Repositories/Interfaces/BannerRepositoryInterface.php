<?php

namespace App\Repositories\Interfaces;

use App\DTOs\V1\Banner\ListBannerDTO;
use App\Models\Banner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BannerRepositoryInterface
{
    public function getActiveBanners(ListBannerDTO $dto): LengthAwarePaginator|Collection;
    public function findById(int $id): ?Banner;
}
