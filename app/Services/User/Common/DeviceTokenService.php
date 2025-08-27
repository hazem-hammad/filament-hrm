<?php

namespace App\Services\User\Common;

use App\DTOs\V1\User\Common\UpdateDeviceTokenDTO;
use App\Repositories\Interfaces\DeviceTokenRepositoryInterface;

class DeviceTokenService
{
    public function __construct(protected DeviceTokenRepositoryInterface $deviceTokenRepository) {}

    public function updateDeviceToken(UpdateDeviceTokenDTO $dto): void
    {
        $this->deviceTokenRepository->updateDeviceToken($dto);
    }
}
