<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\Collections\NotificationCollection;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\User\Auth\UserService;
use Illuminate\Http\JsonResponse;
use Throwable;

class NotificationController extends Controller
{
    public function __construct(
        public UserService $userService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $notifications = $this->userService->getNotifications();

            return (new DataResponse(new NotificationCollection($notifications)))->toJson();
        } catch (Throwable $exception) {
            app('custom.logger')->error(__METHOD__, $exception);

            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}
