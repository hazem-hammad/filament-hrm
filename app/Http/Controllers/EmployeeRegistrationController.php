<?php

namespace App\Http\Controllers;

use App\DTOs\V1\Employee\RegisterEmployeeDTO;
use App\Http\Requests\V1\Employee\RegisterEmployeeRequest;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\Employee\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

class EmployeeRegistrationController extends Controller
{
    public function __construct(private readonly EmployeeService $employeeService) {}

    /**
     * Show the employee registration form
     */
    public function showRegistrationForm(): View
    {
        return view('employee.register');
    }

    /**
     * Handle employee registration
     */
    public function register(RegisterEmployeeRequest $request): RedirectResponse
    {
        try {
            $dto = new RegisterEmployeeDTO($request->validated());
            $employee = $this->employeeService->registerEmployee($dto);
            
            return redirect()->route('employee.registration.success')
                ->with('success', 'Registration successful! Please check your email for login credentials.');
        } catch (Exception $e) {
            app('custom.logger')->error(__METHOD__, $e);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show registration success page
     */
    public function success(): View
    {
        return view('employee.registration-success');
    }

    /**
     * API endpoint for employee registration
     */
    public function apiRegister(RegisterEmployeeRequest $request): JsonResponse
    {
        try {
            $dto = new RegisterEmployeeDTO($request->validated());
            $employee = $this->employeeService->registerEmployee($dto);
            
            return (new DataResponse($employee, 'Employee registered successfully'))->toJson();
        } catch (Exception $e) {
            app('custom.logger')->error(__METHOD__, $e);
            return (new ErrorResponse($e->getMessage(), [], 400))->toJson();
        }
    }
}