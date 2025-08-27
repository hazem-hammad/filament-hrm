<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\Common\UploadFileRequest;
use App\Http\Resources\Common\FileUploadResource;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\User\Common\UploadFileService;
use Illuminate\Http\JsonResponse;

class UploadController extends Controller
{
    public function __invoke(
        UploadFileRequest $request,
        UploadFileService $uploadFileService
    ): JsonResponse {
        try {
            $file = $uploadFileService->uploadFile($request->file('file'));

            return (new DataResponse(new FileUploadResource($file)))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
