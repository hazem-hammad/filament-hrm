<?php

namespace App\Repositories;

use App\DTOs\V1\User\Auth\LoginSocialDTO;
use App\DTOs\V1\User\Auth\RegisterUserDTO;
use App\DTOs\V1\User\Expert\ListExpertDTO;
use App\DTOs\V1\User\Profile\CompleteProfileDTO;
use App\DTOs\V1\User\Profile\StoreServiceDTO;
use App\DTOs\V1\User\Profile\UpdateProfileDTO;
use App\DTOs\V1\User\Profile\UpdateServiceDTO;
use App\Enum\UserStatus;
use App\Enum\UserType;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    // Old create method removed for new registration structure

    public function create(RegisterUserDTO $dto)
    {

        $user = User::create([
            'username' => $dto->getUsername(),
            'first_name' => $dto->getFirstName(),
            'last_name' => $dto->getLastName(),
            'password' => Hash::make($dto->getPassword()),
            'birthdate' => $dto->getBirthdate(),
            'nationality_id' => $dto->getNationalityId(),
            'type' => $dto->getUserType(),
            'email' => $dto->getEmail(),
            'status' => UserStatus::ACTIVE->value,
            'email_verified_at' => now(),
            'complete_profile_step' => 0,
        ]);

        $user->languages()->sync($dto->getLanguages());

        return $user->refresh();
    }

    public function register(RegisterUserDTO $dto)
    {
        return User::create([
            'username' => $dto->getUsername(),
            'first_name' => $dto->getFirstName(),
            'last_name' => $dto->getLastName(),
            'email' => $dto->getEmail(),
            'phone' => $dto->getPhone(),
            'country_code' => $dto->getCountryCode(),
            'password' => Hash::make($dto->getPassword()),
            'birthdate' => $dto->getBirthdate(),
            'user_type' => $dto->getUserType(),
            'status' => UserStatus::ACTIVE->value,
            'email_verified_at' => now(),
        ]);
    }

    public function verifyEmail($userId)
    {
        User::find($userId)->update([
            'email_verified_at' => now(),
        ]);
    }

    public function updateProfile(UpdateProfileDTO $dto, $id)
    {
        $data = array_filter([
            'first_name' => $dto->getFirstName(),
            'last_name' => $dto->getLastName(),
            'bio' => $dto->getBio(),
            'birthdate' => $dto->getBirthdate(),
            'nationality_id' => $dto->getNationailtyId(),
        ]);
        $user = User::find($id);
        if ($dto->getLanguages() !== null) {
            $user->languages()->sync($dto->getLanguages());
        }
        $user->update($data);

        return $user->refresh();
    }

    public function completeProfile(CompleteProfileDTO $dto, $id)
    {

        $data = array_filter([
            'username' => $dto->getUsername(),
            'password' => Hash::make($dto->getPassword()),
            'birthdate' => $dto->getBirthdate(),
            'nationality_id' => $dto->getNationalityId(),
            'type' => $dto->getUserType(),
            'status' => UserStatus::ACTIVE->value,
            'email_verified_at' => now(),
            'complete_profile_step' => 0,
        ]);

        // to be updated to null on case of social login if profile not completed
        $data['first_name'] = $dto->getFirstName();
        $data['last_name'] = $dto->getLastName();
        $data['email'] = $dto->getEmail();

        $user = User::find($id);
        if ($dto->getLanguages()) {
            $user->languages()->sync($dto->getLanguages());
        }
        $user->update($data);

        return $user->refresh();
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function findByUsername(string $username)
    {
        return User::where('username', $username)->first();
    }

    public function findByPhoneAndCountryCode(string $phone, string $countryCode): ?User
    {
        return User::where('phone', $phone)
            ->where('country_code', $countryCode)
            ->first();
    }

    public function findById($userId)
    {
        return User::where('id', $userId)->active()->first();
    }

    public function findByIds(array $ids)
    {
        return User::whereIn('id', $ids)->get();
    }

    public function createPasswordToken($userId, $token)
    {
        DB::table('password_reset_tokens')
            ->insert([
                'token' => $token,
                'user_id' => $userId,
                'created_at' => now(),
            ]);

        return $token;
    }

    public function getPasswordResetToken($accessToken)
    {
        return DB::table('password_reset_tokens')
            ->where('token', $accessToken)
            ->first();
    }

    public function deletePasswordResetToken($passwordResetToken)
    {
        DB::table('password_reset_tokens')
            ->where('token', $passwordResetToken)
            ->delete();
    }

    public function checkOldPassword($oldPassword, $newPassword): bool
    {
        return Hash::check($newPassword, $oldPassword);
    }

    public function resetPassword($user, $newPassword)
    {
        return $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function changeEmail($user, $email)
    {
        return $user->update([
            'email' => $email,
        ]);
    }

    public function getBySocialId($dto): ?User
    {
        return User::where('social_id', $dto->getSocialId())->first();
    }

    public function createUserSocial(LoginSocialDTO $dto): ?User
    {
        return User::query()->create(
            [
                'social_id' => $dto->getSocialId(),
                'social_type' => $dto->getSocialType(),
                'email' => $dto->getEmail(),
                'first_name' => $dto->getFirstName(),
                'last_name' => $dto->getLastName(),
            ]
        );
    }

    public function getActiveAndHasDeviceTokenUsers(int $chunk, $callback)
    {
        $users = User::where('status', UserStatus::ACTIVE->value)
            ->whereHas('tokens', function ($query) {
                $query->whereNotNull('device_token');
            })
            ->with(['tokens' => function ($query) {
                $query->whereNotNull('device_token');
            }])
            ->chunk($chunk, $callback);

        return $users;
    }

    public function findWithRelations(int $id): ?User
    {
        return User::with([
            'media',
            'expertServices.category',
            'favorites' => function ($query) {
                $currentUser = $this->getProfile();
                $query->when($currentUser, function ($q) use ($currentUser) {
                    $q->where('user_id', $currentUser->id);
                });
            },
        ])->findOrFail($id);
    }

    public function getProfile()
    {
        return auth('api')->user();
    }

    public function getByIdentifier(string $identifier): ?User
    {
        return User::where('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();
    }

    public function getUserFromIdentifier(string $identifier): ?User
    {
        $query = User::query()->whereNull('deleted_at');

        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? $query->where('email', $identifier)->first()
            : $query->where('username', $identifier)->first();

        return $user;
    }

    public function updateAvailability($dto, $userId): void
    {
        DB::transaction(function () use ($dto, $userId) {
            $submittedDays = collect($dto)->pluck('day_number')->toArray();
            $user = $this->findById($userId);
            $user->availabilities()->whereNotIn('day_number', $submittedDays)->delete();
            foreach ($dto as $availability) {
                $user->availabilities()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'day_number' => $availability['day_number'],
                    ],
                    [
                        'start_time' => $availability['from'],
                        'end_time' => $availability['to'],
                    ]
                );
            }

            $user->update(['complete_profile_step' => 2]);
        });
    }

    public function listExperts(ListExpertDTO $dto): LengthAwarePaginator
    {
        return User::query()
            ->active()->filter()
            ->where('type', UserType::EXPERT->value)
            ->when(auth('api')->check(), function ($query) {
                $query->where('id', '!=', $this->getProfile()?->id);
            })
            ->with([
                'favorites',
                'languages',
                'expertServices',
            ])
            ->paginate($dto->getLimit(), ['*'], 'page', $dto->getPage());
    }

    public function storeServices(StoreServiceDTO $dto, int $userId): void
    {
        $services = $dto->getServices();
        DB::transaction(function () use ($userId, $services) {
            $user = $this->findById($userId);

            if ($user->complete_profile_step != 2) {
                $user->update(['complete_profile_step' => 1]);
            }

            $syncData = collect($services)->mapWithKeys(function ($service) {
                return [
                    $service['service_id'] => [
                        'hour_rate' => $service['hour_rate'],
                        'bio' => $service['bio'],
                    ],
                ];
            })->toArray();

            $user->expertServices()->attach($syncData);
        });
    }

    /**
     * Get pricing configuration for experts
     */
    public function getPricingConfiguration(): array
    {
        return [
            'min' => 0,
            'max' => User::getExpertServicesMaxRate(),
        ];
    }

    public function getAvailabilities(User $user, int $dayNumber)
    {
        return $user->expertAvailabilities()
            ->where('day_number', $dayNumber)
            ->orderBy('start_time')
            ->get();
    }

    public function getMyAvailabilities()
    {
        $user = User::findOrFail($this->getProfile()->id);

        return $user->expertAvailabilities()->get();
    }

    public function getReservations(User $user, string $date)
    {
        return $user->expertReservations()
            ->whereDate('date', $date)
            ->orderBy('start_time')
            ->get();
    }

    public function updateService(UpdateServiceDTO $dto, int $id)
    {
        $user = User::findOrFail($this->getProfile()->id);

        return $user->expertServices()->updateExistingPivot($id, [
            'hour_rate' => $dto->getHourRate(),
            'bio' => $dto->getBio(),
        ]);
    }

    public function deleteService(int $id)
    {
        $user = User::findOrFail($this->getProfile()->id);

        return $user->expertServices()->detach($id);
    }
}
