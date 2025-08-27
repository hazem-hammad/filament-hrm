<?php

namespace App\Repositories\Interfaces;

interface DeviceTokenRepositoryInterface
{
    public function updateDeviceToken($dto): void;
}
