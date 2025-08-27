<?php

namespace App\Http\Controllers\Api\V1\User\Profile;

use App\DTOs\V1\User\Profile\UpdatePasswordDTO;
use App\DTOs\V1\User\Profile\UpdateProfileDTO;
use App\Exception\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\Profile\UpdatePasswordRequest;
use App\Http\Requests\V1\User\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\User\Auth\UserService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function get(): JsonResponse
    {
        try {
            $user = $this->userService->getProfile();
            $resource = new ProfileResource($user);

            return (new DataResponse($resource))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('failed to get profile')))->toJson();
        }
    }

    /**
     * Update user profile.
     */
    

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $dto = new UpdateProfileDTO($request->validated());

            $user = $this->userService->updateProfile($dto);
            $resource = new ProfileResource($user);

            return (new DataResponse($resource))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('failed to update profile')))->toJson();
        }
    }

    public function changePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $dto = new UpdatePasswordDTO($request->validated());
            $this->userService->changePassword($dto);

            return (new DataResponse(null, __('updated_successfully')))->toJson();
        } catch (CustomException $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse($exception->getMessage(), [], Response::HTTP_BAD_REQUEST))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }

    public function deleteAccount(): JsonResponse
    {
        try {
            $this->userService->deleteUser();

            return (new DataResponse(null, __('deleted_successfully')))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
