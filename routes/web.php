<?php

use App\Http\Controllers\CareerController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\EmployeePasswordSetupController;
use Illuminate\Support\Facades\Route;

// Employee password setup routes
Route::get('/employee/setup-password/{token}', [EmployeePasswordSetupController::class, 'showSetupForm'])
    ->name('employee.setup-password');
Route::post('/employee/setup-password/{token}', [EmployeePasswordSetupController::class, 'setupPassword'])
    ->name('employee.setup-password.store');

// Main application routes (protected by setup check)
Route::get('/', function () {
    return view('welcome');
});

// dynamic route for company-name
Route::get('/careers', [CareerController::class, 'index'])->name('jobs.index');
// dynamic route for each career
Route::get('/careers/{slug}', [CareerController::class, 'show'])->name('jobs.show');
// route for job application submission
Route::post('/careers/{slug}/apply', [CareerController::class, 'apply'])
    ->middleware(['enhanced_csrf', 'rate_limit:job_application', 'recaptcha'])
    ->name('job.apply');

// Employee attendance duration API for real-time updates
Route::middleware(['auth:employee'])->group(function () {
    Route::get('/employee/attendance/duration', [App\Http\Controllers\Api\V1\Employee\AttendanceController::class, 'getDuration']);
});
