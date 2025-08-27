<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\DTOs\V1\Article\ListArticleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Article\ListArticleRequest;
use App\Http\Resources\V1\Article\ArticleResource;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\Article\ArticleService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $service) {}

    public function index(ListArticleRequest $request): JsonResponse
    {
        try {
            $dto = new ListArticleDTO($request->validated());
            $articles = $this->service->list($dto);
            
            return (new DataResponse(
                ArticleResource::collection($articles),
                __('Articles retrieved successfully')
            ))->toJson();
        } catch (\Throwable $e) {
            app('custom.logger')->error(__METHOD__, $e);
            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}