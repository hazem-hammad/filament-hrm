<?php

namespace App\Http\Requests\V1\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class CheckUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'min:8', 'max:15'],
            'country_code' => ['required', 'string', 'min:1', 'max:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('Phone number is required'),
            'phone.min' => __('Phone number must be at least 8 characters'),
            'phone.max' => __('Phone number must not exceed 15 characters'),
            'country_code.required' => __('Country code is required'),
            'country_code.min' => __('Country code must be at least 1 character'),
            'country_code.max' => __('Country code must not exceed 4 characters'),
        ];
    }
}
