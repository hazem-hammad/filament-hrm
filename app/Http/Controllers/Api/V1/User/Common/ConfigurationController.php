<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\DTOs\V1\User\Common\GetConfigurationDTO;
use App\Http\Controllers\Controller;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\User\Common\SettingsService;
use Illuminate\Http\JsonResponse;

class ConfigurationController extends controller
{
    public function __construct(
        private readonly SettingsService $settingsService
    ) {}

    public function getConfiguration(): JsonResponse
    {
        try {

            $loginOptions = [];
            if (request()->hasHeader('Login-Apple')) {
                $loginOptions['apple'] = booleanValue(request()->header('Login-Apple'));
            }
            if (request()->hasHeader('Login-Google')) {
                $loginOptions['google'] = booleanValue(request()->header('Login-Google'));
            }

            $dto = new GetConfigurationDTO([
                'platform' => request()->header('Platform'),
                'version' => request()->header('Version'),
                'login_options' => ! empty($loginOptions) ? $loginOptions : null,
            ]);

            // Get force update info based on platform and version
            $forceUpdate = $this->settingsService->getForceUpdateInfo(
                $dto->getPlatform() ?? 'ios',
                $dto->getVersion() ?? '1.0.0'
            );
            
            $data = [
                'force_update' => $forceUpdate
            ];

            return (new DataResponse($data))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }

}
