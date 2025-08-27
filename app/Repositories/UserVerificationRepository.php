<?php

namespace App\Repositories;

use App\Models\UserVerification;
use App\Repositories\Interfaces\UserVerificationRepositoryInterface;
use Illuminate\Support\Str;

class UserVerificationRepository implements UserVerificationRepositoryInterface
{
    public function getByToken(string $token): ?UserVerification
    {
        return UserVerification::where('token', $token)->first();
    }

    public function create(string $email, string $action, ?int $userId, ?int $expiryTime = null): UserVerification
    {
        return UserVerification::create([
            'user_id' => $userId,
            'email' => $email,
            'otp' => generateOtp(),
            'token' => Str::random(64),
            'action' => $action,
            'expired_at' => now()->addMinutes($expiryTime ?? config('core.otp.expired', 1)),
        ]);
    }

    public function createForPhone(string $phone, string $countryCode, string $action, ?int $userId, ?int $expiryTime = null): UserVerification
    {
        return UserVerification::create([
            'user_id' => $userId,
            'phone' => $phone,
            'country_code' => $countryCode,
            'otp' => generateOtp(),
            'token' => Str::random(64),
            'action' => $action,
            'expired_at' => now()->addMinutes($expiryTime ?? config('core.otp.expired', 5)),
        ]);
    }

    public function clear($email): void
    {
        UserVerification::where('email', $email)->delete();
    }
}
