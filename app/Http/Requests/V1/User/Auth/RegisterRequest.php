<?php

namespace App\Http\Requests\V1\User\Auth;

use App\Enum\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'username' => ['required', 'string', 'min:3', 'max:30', Rule::unique('users', 'username'), 'regex:/^[a-zA-Z0-9_-]{3,30}$/'],
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name' => ['required', 'string', 'min:2', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'min:8', 'max:15'],
            'country_code' => ['required', 'string', 'max:5'],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'birthdate' => ['required', 'date', 'date_format:d-m-Y', 'before:today'],
            'user_type' => ['required', 'string', 'max:50', Rule::in(UserType::getEntities())],
            'verification_token' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'username.required' => __('validation.required', ['attribute' => __('validation.attributes.username')]),
            'username.unique' => __('validation.unique', ['attribute' => __('validation.attributes.username')]),
            'username.regex' => __('validation.custom.username.regex'),
            'first_name.required' => __('validation.required', ['attribute' => __('validation.attributes.first_name')]),
            'first_name.min' => __('validation.min.string', ['attribute' => __('validation.attributes.first_name'), 'min' => 2]),
            'last_name.required' => __('validation.required', ['attribute' => __('validation.attributes.last_name')]),
            'last_name.min' => __('validation.min.string', ['attribute' => __('validation.attributes.last_name'), 'min' => 2]),
            'email.required' => __('validation.required', ['attribute' => __('validation.attributes.email')]),
            'email.email' => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
            'phone.required' => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'country_code.required' => __('validation.required', ['attribute' => __('validation.attributes.country_code')]),
            'password.required' => __('validation.required', ['attribute' => __('validation.attributes.password')]),
            'birthdate.required' => __('validation.required', ['attribute' => __('validation.attributes.birthdate')]),
            'birthdate.date_format' => __('validation.custom.birthdate.date_format'),
            'birthdate.before' => __('validation.custom.birthdate.before'),
            'user_type.required' => __('validation.required', ['attribute' => __('validation.attributes.user_type')]),
            'user_type.in' => __('validation.custom.user_type.in'),
            'verification_token.required' => __('validation.custom.verification_token.required'),
        ];
    }
}