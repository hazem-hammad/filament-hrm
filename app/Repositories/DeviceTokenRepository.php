<?php

namespace App\Repositories;

use App\Repositories\Interfaces\DeviceTokenRepositoryInterface;

class DeviceTokenRepository implements DeviceTokenRepositoryInterface
{
    public function updateDeviceToken($dto): void
    {
        $token = auth('api')->user()->currentAccessToken();

        $token->device_token = $dto->getDeviceToken();
        $token->user_language = $dto->getUserLanguage();

        $token->save();
    }
}
