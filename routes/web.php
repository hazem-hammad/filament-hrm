<?php

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
