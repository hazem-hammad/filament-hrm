<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class PhoneUnique implements ValidationRule
{
    /**
     * Create a new rule instance.
     */
    public function __construct(
        private string $table,
        private ?int $exceptId = null,
        private string $phoneCodeColumn = 'phone_code',
        private string $phoneColumn = 'phone',
        private string $deletedAtColumn = 'deleted_at'
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
            ->where($this->phoneColumn, $phoneNumber)
            ->whereNull($this->deletedAtColumn); // Ignore soft-deleted records

        // Exclude current record if updating
        if ($this->exceptId !== null) {
            $query->where('id', '!=', $this->exceptId);
        }

        // Check if phone number exists
        if ($query->exists()) {
            $fail(__('validation_phone_unique'));
        }
    }

    /**
     * Exclude a record from the uniqueness check.
     */
    public function ignore(?int $id): self
    {
        $this->exceptId = $id;

        return $this;
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
}
