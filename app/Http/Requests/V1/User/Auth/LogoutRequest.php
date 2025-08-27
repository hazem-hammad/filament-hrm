<?php

namespace App\Http\Requests\V1\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
{
    /**
     * Allow all users to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define the validation rules for the registration form.
     */
    public function rules(): array
    {
        return [
            'device_token' => ['nullable', 'string'],
        ];
    }
}
