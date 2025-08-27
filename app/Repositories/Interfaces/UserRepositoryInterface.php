<?php

namespace App\Repositories\Interfaces;

use App\DTOs\V1\User\Auth\LoginSocialDTO;
use App\DTOs\V1\User\Auth\RegisterUserDTO;
use App\DTOs\V1\User\Expert\ListExpertDTO;
use App\DTOs\V1\User\Profile\CompleteProfileDTO;
use App\DTOs\V1\User\Profile\StoreServiceDTO;
use App\DTOs\V1\User\Profile\UpdateProfileDTO;
use App\DTOs\V1\User\Profile\UpdateServiceDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function create(RegisterUserDTO $dto);

    public function register(RegisterUserDTO $dto);

    public function verifyEmail($userId);

    public function findByEmail(string $email);

    public function findByUsername(string $username);

    public function findByPhoneAndCountryCode(string $phone, string $countryCode): ?User;

    public function updateProfile(UpdateProfileDTO $dto, $id);

    public function completeProfile(CompleteProfileDTO $dto, $id);

    public function findById($userId);

    public function findByIds(array $ids);

    public function createPasswordToken($userId, $token);

    public function getPasswordResetToken($accessToken);

    public function deletePasswordResetToken($passwordResetToken);

    public function checkOldPassword($oldPassword, $newPassword): bool;

    public function resetPassword($user, $newPassword);

    public function changeEmail($user, $email);

    public function getBySocialId(LoginSocialDTO $dto): ?User;

    public function findWithRelations(int $id): ?User;

    public function createUserSocial(LoginSocialDTO $dto): ?User;

    public function getActiveAndHasDeviceTokenUsers(int $chunk, $callback);

    public function getByIdentifier(string $identifier): ?User;

    public function getUserFromIdentifier(string $identifier): ?User;

    public function listExperts(ListExpertDTO $dto): LengthAwarePaginator;

    public function storeServices(StoreServiceDTO $dto, int $userId): void;

    public function updateService(UpdateServiceDTO $dto, int $id);

    public function updateAvailability($dto, $userId): void;

    public function getPricingConfiguration(): array;

    public function deleteService(int $id);

    public function getMyAvailabilities();
}
