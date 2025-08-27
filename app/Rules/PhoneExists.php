<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class PhoneExists implements ValidationRule
{
    /**
     * Create a new rule instance.
     */
    public function __construct(
        private string $table,
        private string $phoneCodeColumn = 'phone_code',
        private string $phoneColumn = 'phone',
        private ?array $additionalWhere = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Split the phone number into code and number
        $parts = explode('-', $value);

        if (count($parts) !== 2) {
            $fail(__('validation_phone_format'));

            return;
        }

        [$phoneCode, $phoneNumber] = $parts;

        // Build the query
        $query = DB::table($this->table)
            ->where($this->phoneCodeColumn, $phoneCode)
            ->where($this->phoneColumn, $phoneNumber);

        // Add additional where conditions if specified
        if ($this->additionalWhere) {
            foreach ($this->additionalWhere as $column => $value) {
                if (is_array($value)) {
                    $query->whereIn($column, $value);
                } else {
                    $query->where($column, $value);
                }
            }
        }

        // Check if phone number exists
        if (! $query->exists()) {
            $fail(__('validation_phone_not_exists'));
        }
    }

    /**
     * Specify custom column names.
     */
    public function using(string $phoneCodeColumn, string $phoneColumn): self
    {
        $this->phoneCodeColumn = $phoneCodeColumn;
        $this->phoneColumn = $phoneColumn;

        return $this;
    }

    /**
     * Add additional where conditions
     */
    public function where(array $conditions): self
    {
        $this->additionalWhere = $conditions;

        return $this;
    }
}
