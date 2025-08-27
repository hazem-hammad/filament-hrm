<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\DTOs\V1\User\Common\GetPageDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\Information\GetPageBySlugRequest;
use App\Http\Resources\PageResource;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\User\Common\InformationService;
use Illuminate\Http\JsonResponse;

class InformationController extends Controller
{
    public function __construct(private InformationService $InformationService) {}

    public function getPage(GetPageBySlugRequest $request, string $slug): JsonResponse
    {
        try {
            $dto = new GetPageDTO([
                'page_slug' => $slug,
            ]);

            $data = $this->InformationService->getPageBySlug($dto);
            $resource = new PageResource($data);

            return (new DataResponse($resource))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
