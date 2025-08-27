<?php

namespace App\Http\Controllers\Api\V1\Banner;

use App\DTOs\V1\Banner\ListBannerDTO;
use App\Exception\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Banner\ListBannerRequest;
use App\Http\Resources\V1\Banner\BannerResource;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\Banner\BannerService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class BannerController extends Controller
{
    public function __construct(
        private readonly BannerService $bannerService
    ) {}

    public function index(ListBannerRequest $request): JsonResponse
    {
        try {
            $dto = new ListBannerDTO($request->validated());
            $banners = $this->bannerService->getActiveBanners($dto);

            $resource = BannerResource::collection($banners);

            return (new DataResponse($resource, __('Banners retrieved successfully')))->toJson();
        } catch (CustomException $exception) {
            app('custom.logger')->error(__METHOD__, $exception);
            return (new ErrorResponse($exception->getMessage(), [], Response::HTTP_BAD_REQUEST))->toJson();
        } catch (\Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);
            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
