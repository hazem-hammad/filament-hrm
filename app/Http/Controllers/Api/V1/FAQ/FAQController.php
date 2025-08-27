<?php

namespace App\Http\Controllers\Api\V1\FAQ;

use App\DTOs\V1\FAQ\ListFAQDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FAQ\ListFAQRequest;
use App\Http\Resources\V1\Faq\FaqResource;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\FAQ\FAQService;
use Illuminate\Http\JsonResponse;

class FAQController extends Controller
{
    public function __construct(private FAQService $faqService) {}

    public function index(ListFAQRequest $request): JsonResponse
    {
        try {
            $dto = new ListFAQDTO($request->validated());
            $faqs = $this->faqService->list($dto);

            return (new DataResponse(
                FaqResource::collection($faqs),
                __('FAQs retrieved successfully')
            ))->toJson();
        } catch (\Throwable $e) {
            app('custom.logger')->error(__METHOD__, $e);
            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
