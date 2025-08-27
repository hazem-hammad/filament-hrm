<?php

namespace App\Http\Requests\V1\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
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
            'identifier' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, $value, \Closure $fail) {
                    if (! filter_var($value, FILTER_VALIDATE_EMAIL) && ! $this->isValidUsername($value)) {
                        $fail(__('validation.custom.identifier.invalid_format'));
                    }
                },
            ],
            'password' => ['required', Password::min(8), 'max:255'],
        ];
    }

    /**
     * Check if the value is a valid username format
     */
    private function isValidUsername(string $value): bool
    {
        return preg_match('/^[a-zA-Z0-9_-]{3,30}$/', $value);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'identifier.required' => __('validation.required', ['attribute' => __('validation.attributes.identifier')]),
            'identifier.string' => __('validation.string', ['attribute' => __('validation.attributes.identifier')]),
            'identifier.max' => __('validation.max.string', ['attribute' => __('validation.attributes.identifier'), 'max' => 255]),
            'password.required' => __('validation.required', ['attribute' => __('validation.attributes.password')]),
            'password.min' => __('validation.min.string', ['attribute' => __('validation.attributes.password'), 'min' => 8]),
            'password.max' => __('validation.max.string', ['attribute' => __('validation.attributes.password'), 'max' => 255]),
        ];
    }
}
