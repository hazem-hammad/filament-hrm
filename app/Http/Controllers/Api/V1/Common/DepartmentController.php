<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $departments = Department::query()
                ->active()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            return (new DataResponse($departments))->toJson();
        } catch (\Throwable $e) {
            app('custom.logger')->error(__METHOD__, $e);
            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }

    public function getPositions(int $departmentId): JsonResponse
    {
        try {
            $department = Department::query()
                ->active()
                ->find($departmentId);

            if (!$department) {
                return (new ErrorResponse(__('Department not found'), [], Response::HTTP_NOT_FOUND))->toJson();
            }

            $positions = Position::query()
                ->where('department_id', $departmentId)
                ->active()
                ->select(['id', 'name'])
                ->get();

            return (new DataResponse($positions))->toJson();
        } catch (\Throwable $e) {
            app('custom.logger')->error(__METHOD__, $e);
            return (new ErrorResponse(__('something_went_wrong')))->toJson();
        }
    }
}