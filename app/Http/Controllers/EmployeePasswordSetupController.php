<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class EmployeePasswordSetupController extends Controller
{
    public function showSetupForm(Request $request, string $token)
    {
        // Verify the token
        $tokenData = cache()->get("employee_password_setup_{$token}");
        
        if (!$tokenData) {
            return view('employee.password-setup-expired');
        }
        
        $employee = Employee::find($tokenData['employee_id']);
        
        if (!$employee) {
            return view('employee.password-setup-expired');
        }
        
        return view('employee.password-setup', [
            'token' => $token,
            'employee' => $employee
        ]);
    }
    
    public function setupPassword(Request $request, string $token)
    {
        // Verify the token
        $tokenData = cache()->get("employee_password_setup_{$token}");
        
        if (!$tokenData) {
            return back()->withErrors(['token' => 'This password setup link has expired.']);
        }
        
        $employee = Employee::find($tokenData['employee_id']);
        
        if (!$employee) {
            return back()->withErrors(['token' => 'Invalid employee.']);
        }
        
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Update password
        $employee->update([
            'password' => Hash::make($request->password),
            'password_set_at' => now(),
        ]);
        
        // Remove the token from cache
        cache()->forget("employee_password_setup_{$token}");
        
        return view('employee.password-setup-success', ['employee' => $employee]);
    }
}
