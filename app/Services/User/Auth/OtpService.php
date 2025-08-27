<?php

namespace App\Services\User\Auth;

use App\DTOs\V1\User\Auth\SendOtpDto;
use App\DTOs\V1\User\Auth\VerifyOtpDto;
use App\Enum\OtpActions;
use App\Exception\InvalidOtpException;
use App\Exception\InvalidTokenTypeException;
use App\Exception\OtpExpiredException;
use App\Exception\OtpNotFoundException;
use App\Exception\UserSuspendedException;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\UserVerificationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OtpService
{
    const OTP_EXPIRATION__MINUTES_VERIFY_EMAIL = 5;

    public function __construct(
        private readonly UserVerificationRepositoryInterface $userVerificationRepository,
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function sendOtp(SendOtpDto $dto): ?string
    {
        $user = $this->userRepository->getUserFromIdentifier($dto->getIdentifier());
        if ($user && $user->isSuspended()) {
            throw new UserSuspendedException;
        }

        $email = $this->resolveEmailForAction($dto);

        $token = null;

        DB::transaction(function () use (&$token, $dto, $user, $email) {
            $this->userVerificationRepository->clear($email);
            $token = $this->userVerificationRepository->create($email, $dto->getAction(), $user?->id)->token;
        });

        return $token;
    }

    public function verifyOtp(VerifyOtpDto $dto)
    {
        $token = $this->userVerificationRepository->getByToken($dto->getVerificationToken());
        $this->isOtpValid($token, $dto);

        $action = $token->action;
        $user = $this->userRepository->findByEmail($token->email);
        $verificationToken = null;
        DB::transaction(function () use ($action, $token, &$user, &$verificationToken) {
            $this->userVerificationRepository->clear($token->email);
            switch ($action) {
                case OtpActions::VERIFY_EMAIL->value:
                    $verificationToken = $this->userVerificationRepository->create($token->email, OtpActions::COMPLETE_PROFILE->value, $user?->id, self::OTP_EXPIRATION__MINUTES_VERIFY_EMAIL)?->token;
                    break;
                case OtpActions::RESET_PASSWORD->value:
                    $verificationToken = $this->userRepository->createPasswordToken($user->id, $token->token);
            }
        });

        return $verificationToken;
    }

    private function isOtpValid($token, $dto): void
    {
        if (! $token) {
            throw new OtpNotFoundException;
        }

        if ($token->isExpired()) {
            throw new OtpExpiredException;
        }

        if ($token->otp != $dto->getCode()) {
            throw new InvalidOtpException;
        }
    }

    private function resolveEmailForAction(SendOtpDto $dto): ?string
    {
        return match ($dto->getAction()) {
            OtpActions::VERIFY_EMAIL->value => $dto->getIdentifier(),
            OtpActions::RESET_PASSWORD->value => $this->getUserEmailFromIdentifier($dto->getIdentifier()),
            default => throw new InvalidTokenTypeException,
        };
    }

    private function getUserEmailFromIdentifier(string $identifier): ?string
    {
        $user = $this->userRepository->getUserFromIdentifier($identifier);

        return $user?->email;
    }
}
