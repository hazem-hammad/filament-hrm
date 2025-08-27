<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\DTOs\V1\User\Common\UpdateDeviceTokenDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\DeviceToken\UpdateDeviceTokenRequest;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\User\Common\DeviceTokenService;
use Illuminate\Http\JsonResponse;

class DeviceTokenController extends Controller
{
    public function __construct(private DeviceTokenService $deviceTokenService) {}

    public function updateDeviceToken(UpdateDeviceTokenRequest $request): JsonResponse
    {
        try {
            $dto = new UpdateDeviceTokenDTO([
                'device_token' => request('device_token'),
                'user_language' => request()->header('Accept-Language'),
            ]);

            $this->deviceTokenService->updateDeviceToken($dto);

            return (new DataResponse(null, __('updated_successfully')))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
