<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class PhoneUniqueWithoutPhoneCode implements ValidationRule
{
    /**
     * Create a new rule instance.
     */
    public function __construct(
        private string $table,
        private ?int $exceptId = null,
        private string $phoneColumn = 'phone',
        private string $deletedAtColumn = 'deleted_at'
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Build the query
        $query = DB::table($this->table)
            ->where($this->phoneColumn, $value)
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
    public function using(string $phoneColumn): self
    {
        $this->phoneColumn = $phoneColumn;

        return $this;
    }
}
