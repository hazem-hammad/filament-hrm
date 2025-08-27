<?php

namespace App\Http\Controllers\Api\V1\User\Auth;

use App\DTOs\V1\User\Auth\CheckUserDTO;
use App\DTOs\V1\User\Auth\LoginUserDTO;
use App\DTOs\V1\User\Auth\RegisterUserDTO;
use App\DTOs\V1\User\Auth\ResetPasswordDTO;
use App\Exception\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\Auth\CheckUserRequest;
use App\Http\Requests\V1\User\Auth\LoginRequest;
use App\Http\Requests\V1\User\Auth\LogoutRequest;
use App\Http\Requests\V1\User\Auth\RegisterRequest;
use App\Http\Requests\V1\User\Auth\ResetPasswordRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\User\Auth\CheckUserService;
use App\Services\User\Auth\UserService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly CheckUserService $checkUserService,
        
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = new LoginUserDTO($request->validated());

            $loggedUser = $this->userService->loginUser($dto);
            $token = $this->userService->generateToken($loggedUser);
            $resource = new ProfileResource($loggedUser, $token);

            return (new DataResponse($resource))->toJson();
        } catch (CustomException $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse($exception->getMessage(), [], Response::HTTP_BAD_REQUEST))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $dto = new ResetPasswordDTO($request->validated());

            $user = $this->userService->resetPasswordUser($dto);

            return (new DataResponse(null, __('password reset successfully')))->toJson();
        } catch (CustomException $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse($exception->getMessage(), [], Response::HTTP_BAD_REQUEST))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }

    public function logout(LogoutRequest $request): JsonResponse
    {
        try {
            $this->userService->logoutUser();

            return (new DataResponse(null, __('logged_out_successfully')))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }

    public function checkUser(CheckUserRequest $request): JsonResponse
    {
        try {
            $dto = new CheckUserDTO($request->validated());

            $token = $this->checkUserService->checkUser($dto);

            return (new DataResponse(['token' => $token], __('User verification token created successfully')))->toJson();
        } catch (CustomException $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse($exception->getMessage(), null, Response::HTTP_BAD_REQUEST))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
