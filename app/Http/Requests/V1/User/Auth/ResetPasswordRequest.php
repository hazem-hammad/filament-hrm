<?php

namespace App\Http\Requests\V1\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
            'verification_token' => ['required', 'max:255'],
            'new_password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ];
    }
}
