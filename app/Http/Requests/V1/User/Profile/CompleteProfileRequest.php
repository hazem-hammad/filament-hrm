<?php

namespace App\Http\Requests\V1\User\Profile;

use App\Enum\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CompleteProfileRequest extends FormRequest
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
        $userId = $this->user()->id;

        return [
            'username' => ['required', 'string', 'min:2', 'max:50', Rule::unique('users', 'username')->whereNull('deleted_at')->ignore($userId), 'regex:/^[a-zA-Z0-9_-]{3,30}$/'],
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name' => ['required', 'string', 'min:2', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($userId)],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'birthdate' => ['required', 'date', 'date_format:d-m-Y', 'before:today'],
            'nationality_id' => ['required', 'integer', Rule::exists('nationalities', 'id')],
            'languages' => ['required', 'array', 'min:1'],
            'languages.*' => ['integer', 'exists:languages,id'],
            'image' => ['nullable', 'max:255'],
            'user_type' => ['required', 'string', 'max:50', Rule::in((UserType::values()))],
        ];
    }
}
