<?php

namespace App\Http\Requests\V1\User\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO:: After merging auth make sure to be only for experts
    }

    public function rules(): array
    {
        return [
            'availabilities' => ['required', 'array'],
            'availabilities.*.day_number' => ['required', 'integer', 'min:1', 'max:7'], // 1 (for Monday) through 7 (for Sunday)
            'availabilities.*.from' => ['required', 'date_format:H:i'],
            'availabilities.*.to' => [
                'required',
                'date_format:H:i',
                'after:availabilities.*.from',
            ],
        ];
    }
}
