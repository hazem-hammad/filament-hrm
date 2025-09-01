<?php

namespace App\Rules;

use App\Traits\SanitizesInput;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoSuspiciousContent implements ValidationRule
{
    use SanitizesInput;

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        if ($this->containsSuspiciousPatterns($value)) {
            $fail('The :attribute contains suspicious or potentially harmful content.');
        }
    }
}