<?php

namespace App\Http\Controllers\Api\V1\User\Auth;

use App\DTOs\V1\User\Auth\SendOtpDto;
use App\DTOs\V1\User\Auth\VerifyOtpDto;
use App\Exception\CustomException;
use App\Exception\SameOldEmailException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\Auth\SendOtpRequest;
use App\Http\Requests\V1\User\Auth\VerifyOtpRequest;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\User\Auth\OtpService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OtpController extends Controller
{
    public function __construct(private readonly OtpService $otpService) {}

    public function send(SendOtpRequest $sendOtpRequest): JsonResponse
    {
        try {
            $dto = new SendOtpDto($sendOtpRequest->validated());
            $token = $this->otpService->sendOtp($dto);

            return (new DataResponse(['verification_token' => $token]))->toJson();
        } catch (SameOldEmailException $exception) {
            app('custom.logger')->error(__CLASS__.'|'.__METHOD__, $exception);

            return (new ErrorResponse($exception->getMessage(), [], Response::HTTP_BAD_REQUEST))->toJson();
        } catch (\Exception $exception) {
            app('custom.logger')->error(__CLASS__.'|'.__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }

    public function verify(VerifyOtpRequest $verifyOtpRequest): JsonResponse
    {
        try {
            $dto = new VerifyOtpDto($verifyOtpRequest->validated());
            $verificationToken = $this->otpService->verifyOtp($dto);

            return (new DataResponse(['verification_token' => $verificationToken]))->toJson();
        } catch (CustomException $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse($exception->getMessage(), [], Response::HTTP_BAD_REQUEST))->toJson();
        } catch (\Exception $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
