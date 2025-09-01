<?php

namespace App\Repositories\Interfaces;

use App\DTOs\V1\Asset\CreateAssetDTO;
use App\DTOs\V1\Asset\UpdateAssetDTO;
use App\DTOs\V1\Asset\ListAssetDTO;
use App\Models\Asset;
use Illuminate\Pagination\LengthAwarePaginator;

interface AssetRepositoryInterface
{
    public function create(CreateAssetDTO $dto): Asset;

    public function update(UpdateAssetDTO $dto, int $id): Asset;

    public function findById(int $id): ?Asset;

    public function findByAssetId(string $assetId): ?Asset;

    public function list(ListAssetDTO $dto): LengthAwarePaginator;

    public function delete(int $id): bool;

    public function assignToEmployee(int $assetId, int $employeeId): Asset;

    public function unassignFromEmployee(int $assetId): Asset;

    public function getAvailableAssets(): \Illuminate\Database\Eloquent\Collection;

    public function getAssignedAssets(): \Illuminate\Database\Eloquent\Collection;

    public function getAssetsByEmployee(int $employeeId): \Illuminate\Database\Eloquent\Collection;

    public function getAssetsInMaintenance(): \Illuminate\Database\Eloquent\Collection;

    public function getExpiredWarrantyAssets(): \Illuminate\Database\Eloquent\Collection;

    public function searchAssets(string $search): \Illuminate\Database\Eloquent\Collection;
}