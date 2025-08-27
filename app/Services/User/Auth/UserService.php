<?php

namespace App\Services\User\Auth;

use App\DTOs\V1\User\Auth\LoginSocialDTO;
use App\DTOs\V1\User\Auth\LoginUserDTO;
use App\DTOs\V1\User\Auth\ResetPasswordDTO;
use App\DTOs\V1\User\Profile\CompleteProfileDTO;
use App\DTOs\V1\User\Profile\StoreServiceDTO;
use App\DTOs\V1\User\Profile\UpdatePasswordDTO;
use App\DTOs\V1\User\Profile\UpdateProfileDTO;
use App\DTOs\V1\User\Profile\UpdateServiceDTO;
use App\Enum\MediaCollections;
use App\Enum\OtpActions;
use App\Exception\CannotResetPasswordException;
use App\Exception\CannotUseOldPasswordException;
use App\Exception\DifferentSocialMethodException;
use App\Exception\InvalidCredentialsException;
use App\Exception\InvalidProfileException;
use App\Exception\InvalidTokenTypeException;
use App\Exception\InvalidUserException;
use App\Exception\TokenExpiredException;
use App\Exception\TokenNotFoundException;
use App\Exception\UserSuspendedException;
use App\Exception\WrongPasswordException;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\UserVerificationRepositoryInterface;
use App\Services\User\Common\UploadFileService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UploadFileService $uploadFileService,
        protected UserVerificationRepositoryInterface $userVerificationRepository,
    ) {}

    public function loginUser(LoginUserDTO $dto)
    {
        $identifier = $dto->getIdentifier();

        $user = $this->findUserByIdentifier($identifier);

        if (! $user || ! Hash::check($dto->getPassword(), $user->password)) {
            throw new InvalidCredentialsException;
        }

        if ($user->isSuspended()) {
            throw new UserSuspendedException;
        }

        return $user;
    }

    /**
     * Find user by email or username
     */
    private function findUserByIdentifier(string $identifier)
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->userRepository->findByEmail($identifier);
        }

        return $this->userRepository->findByUsername($identifier);
    }

    public function resetPasswordUser(ResetPasswordDTO $dto)
    {
        $passwordResetToken = $this->userRepository->getPasswordResetToken($dto->getVerificationToken());

        if (! $passwordResetToken) {
            throw new CannotResetPasswordException;
        }

        $user = $this->userRepository->findById($passwordResetToken->user_id);

        if (! $user) {
            throw new CannotResetPasswordException;
        }

        $checkOldPassword = $this->userRepository->checkOldPassword($user->password, $dto->getPassword());

        if ($checkOldPassword) {
            throw new CannotUseOldPasswordException;
        }
        DB::transaction(function () use ($user, $dto, $passwordResetToken) {
            $this->userRepository->resetPassword($user, $dto->getPassword());
            $user->tokens()->delete();
            $this->userRepository->deletePasswordResetToken($passwordResetToken->token);
        });

        return $user;
    }

    public function changePassword(UpdatePasswordDTO $dto)
    {
        $user = auth('api')->user();

        if (! Hash::check($dto->getOldPassword(), $user->password)) {
            throw new WrongPasswordException;
        }

        if ($this->userRepository->checkOldPassword($user->password, $dto->getPassword())) {
            throw new CannotUseOldPasswordException;
        }

        return $this->userRepository->resetPassword($user, $dto->getPassword());
    }

    public function generateToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function getProfile(): Authenticatable
    {
        return auth('api')->user();
    }

    public function getProfileById(int $id): User
    {
        $authUser = $this->getProfile();
        $user = $this->userRepository->findWithRelations($id);
        if (! $user) {
            throw new InvalidUserException;
        }

        if ($user->id != $authUser->id) {
            throw new InvalidProfileException;
        }

        return $user;
    }

    public function updateProfile(UpdateProfileDTO $dto)
    {
        $user = $this->userRepository->updateProfile($dto, auth('api')->id());
        try {
            if ($dto->getImage()) {
                $this->uploadFileService->addMedia($user, $dto->getImage(), MediaCollections::AVATAR->value);
            }
        } catch (\Exception $e) {

            Log::error('failed to upload image  with error: '.$e->getMessage());
        }

        return $user;
    }

    public function logoutUser()
    {
        auth('api')->user()->currentAccessToken()->delete();
    }

    public function deleteUser()
    {
        auth('api')->user()->delete();
    }

    public function socialLogin(LoginSocialDTO $dto)
    {
        $user = $this->userRepository->getBySocialId($dto);

        if (! $user) {
            return $this->userRepository->createUserSocial($dto);
        }

        $this->validateUser($user, $dto);

        return $this->handleExistingUser($user, $dto);
    }

    private function validateUser($user, $dto): void
    {
        if ($user->isSuspended()) {
            throw new UserSuspendedException;
        }

        if ($user->social_type !== $dto->getSocialType()) {
            throw new DifferentSocialMethodException;
        }
    }

    private function handleExistingUser($user, $dto)
    {
        if ($user->isCompletedProfile()) {
            return $user;
        }

        return $this->completeUserProfile($user, $dto);
    }

    private function completeUserProfile($user, $dto)
    {
        $updateProfileData = [
            'first_name' => $dto->getFirstName(),
            'last_name' => $dto->getLastName(),
            'email' => $dto->getEmail(),
        ];

        $updateProfileDTO = new CompleteProfileDTO($updateProfileData);

        return $this->userRepository->completeProfile($updateProfileDTO, $user->id);
    }

    public function getNotifications()
    {
        $user = auth('api')->user();
        $notifications = $user->notifications()->latest()->paginate();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return $notifications;
    }

    public function getActiveAndHasDeviceTokenUsers(int $chunk, $callback)
    {
        return $this->userRepository->getActiveAndHasDeviceTokenUsers($chunk, $callback);
    }

    private function isTokenValid($token): void
    {
        if (! $token) {
            throw new TokenNotFoundException;
        }
        if ($token->isExpired()) {
            throw new TokenExpiredException;
        }
        if ($token->action !== OtpActions::COMPLETE_PROFILE->value) {
            throw new InvalidTokenTypeException;
        }
    }

    public function storeService(StoreServiceDTO $dto, $userId)
    {
        $this->userRepository->storeServices($dto, $userId);
    }

    public function updateService(UpdateServiceDTO $dto, $id)
    {
        $this->userRepository->updateService($dto, $id);
    }

    public function updateAvailability($dto, $userId)
    {
        $this->userRepository->updateAvailability($dto->getAvailabilities(), $userId);
    }

    public function getByIds(array $ids)
    {
        return $this->userRepository->findByIds($ids);
    }

    public function deleteService(int $id)
    {
        return $this->userRepository->deleteService($id);
    }

    public function transactions($id, $dto)
    {
        return $this->walletTransactionRepository->userTransactions($id, $dto);
    }

    public function getAvailabilities(): ?Collection
    {
        return $this->userRepository->getMyAvailabilities();
    }
}
