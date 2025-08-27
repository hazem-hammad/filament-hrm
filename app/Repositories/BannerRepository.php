<?php

namespace App\Repositories;

use App\DTOs\V1\Banner\ListBannerDTO;
use App\Models\Banner;
use App\Repositories\Interfaces\BannerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class BannerRepository implements BannerRepositoryInterface
{
    public function getActiveBanners(ListBannerDTO $dto): LengthAwarePaginator|Collection
    {
        $query = Banner::query()
            ->with(['media'])
            ->activeBetweenDates()
            ->orderBy($dto->getSortBy(), $dto->getSortOrder());

        if ($dto->getSearch()) {
            $search = $dto->getSearch();
            $query->where(function ($q) use ($search) {
                $q->whereJsonContains('title->en', $search)
                    ->orWhereJsonContains('title->ar', $search);
            });
        }

        if ($dto->isPaginated()) {
            return $query->paginate(
                $dto->getLimit(),
                ['*'],
                'page',
                $dto->getPage()
            );
        }

        return $query->get();
    }

    public function findById(int $id): ?Banner
    {
        return Banner::query()
            ->with(['media'])
            ->find($id);
    }
}
