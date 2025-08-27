<?php

namespace App\Services\User\Auth;

use App\DTOs\V1\User\Auth\CheckUserDTO;
use App\Enum\OtpActions;
use App\Exception\UserNotFoundInDatabaseOrErpException;
use App\Models\UserVerification;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\UserVerificationRepositoryInterface;
use App\Services\External\ErpService;
use Illuminate\Support\Facades\DB;

class CheckUserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserVerificationRepositoryInterface $userVerificationRepository,
        private ErpService $erpService
    ) {}

    /**
     * Check if user exists in database or ERP and create verification token
     *
     * @param CheckUserDTO $dto
     * @return string
     * @throws UserNotFoundInDatabaseOrErpException
     */
    public function checkUser(CheckUserDTO $dto): string
    {
        // First, check if user exists in the local database
        $existsInDatabase = $this->checkUserInDatabase($dto->getPhone(), $dto->getCountryCode());

        // If not in database, check ERP system
        $existsInErp = false;

        if (!$existsInDatabase) {
            $existsInErp = $this->erpService->checkUserExists($dto->getPhone(), $dto->getCountryCode());
        }

        // If user doesn't exist in either database or ERP, throw exception
        if (!$existsInDatabase && !$existsInErp) {
            throw new UserNotFoundInDatabaseOrErpException();
        }

        // User exists, create verification record and return token
        return $this->createVerificationToken($dto->getPhone(), $dto->getCountryCode());
    }

    /**
     * Check if user exists in the local database
     *
     * @param string $phone
     * @param string $countryCode
     * @return bool
     */
    private function checkUserInDatabase(string $phone, string $countryCode): bool
    {
        $user = $this->userRepository->findByPhoneAndCountryCode($phone, $countryCode);
        return $user !== null;
    }

    /**
     * Create verification token for phone-based verification
     *
     * @param string $phone
     * @param string $countryCode
     * @return string
     */
    private function createVerificationToken(string $phone, string $countryCode): string
    {
        return DB::transaction(function () use ($phone, $countryCode) {

            $this->clearExistingTokens($phone, $countryCode);

            $verification = $this->userVerificationRepository->createForPhone(
                $phone,
                $countryCode,
                OtpActions::VERIFY_PHONE->value,
                null,
                5
            );

            return $verification->token;
        });
    }

    /**
     * Clear existing verification tokens for the phone
     *
     * @param string $phone
     * @param string $countryCode
     * @return void
     */
    private function clearExistingTokens(string $phone, string $countryCode): void
    {
        UserVerification::where('phone', $phone)
            ->where('country_code', $countryCode)
            ->where('action', OtpActions::VERIFY_PHONE->value)
            ->delete();
    }
}
