<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CurrentPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if value is null or empty
        if (is_null($value) || $value === '') {
            return;
        }

        $employee = Auth::guard('employee')->user();
        
        if (!$employee || !Hash::check($value, $employee->password)) {
            $fail('The current password is incorrect.');
        }
    }
}