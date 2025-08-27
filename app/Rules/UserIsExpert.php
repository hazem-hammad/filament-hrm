<?php

namespace App\Rules;

use App\Enum\UserType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserIsExpert implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (auth('api')->user()->type !== UserType::EXPERT->value) {
            $fail('You must be an expert to add services.');
        }
    }
}
