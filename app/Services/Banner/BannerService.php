<?php

namespace App\Services\Banner;

use App\DTOs\V1\Banner\ListBannerDTO;
use App\Models\Banner;
use App\Repositories\Interfaces\BannerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class BannerService
{
    public function __construct(
        private readonly BannerRepositoryInterface $bannerRepository
    ) {}

    public function getActiveBanners(ListBannerDTO $dto): LengthAwarePaginator|Collection
    {
        return $this->bannerRepository->getActiveBanners($dto);
    }

    public function findById(int $id): ?Banner
    {
        return $this->bannerRepository->findById($id);
    }
}
