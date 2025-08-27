<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViewScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date', 'after_or_equal:today'],
            'duration' => ['required', 'integer', 'in:15,30,60'],
        ];
    }
}
