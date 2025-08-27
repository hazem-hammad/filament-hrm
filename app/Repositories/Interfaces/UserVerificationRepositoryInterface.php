<?php

namespace App\Repositories\Interfaces;

use App\Models\UserVerification;

interface UserVerificationRepositoryInterface
{
    public function getByToken(string $token): ?UserVerification;

    public function clear($email): void;

    public function create(string $email, string $action, ?int $userId, ?int $expiryTime = null): UserVerification;

    public function createForPhone(string $phone, string $countryCode, string $action, ?int $userId, ?int $expiryTime = null): UserVerification;
}
