<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove any whitespace
        $value = trim($value);

        // Basic validation checks
        if (! $this->isValidFormat($value)) {
            $fail(__('validation_phone_format'));

            return;
        }

        // Check if contains only numbers and hyphen
        if (! preg_match('/^[0-9-]+$/', $value)) {
            $fail(__('validation_phone_numeric_only'));

            return;
        }

        // Split the number into parts
        $parts = explode('-', $value);
        if (count($parts) !== 2) {
            $fail(__('validation_phone_format'));

            return;
        }

        // Validate country code part (2-4 digits)
        if (! preg_match('/^[0-9]{2,4}$/', $parts[0])) {
            $fail(__('validation_phone_invalid_prefix'));

            return;
        }

        // Validate phone number part (7-12 digits)
        if (! preg_match('/^[0-9]{7,12}$/', $parts[1])) {
            $fail(__('validation_phone_invalid_number'));

            return;
        }
    }

    /**
     * Check if the phone number format is valid
     */
    private function isValidFormat(string $value): bool
    {
        // Format: XX-XXXXXXX to XXXX-XXXXXXXXXXXX
        return preg_match('/^[0-9]{2,4}-[0-9]{7,12}$/', $value);
    }
}
