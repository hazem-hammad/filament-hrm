<?php

namespace App\Http\Requests\V1\User\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'max:255'],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'max:255'],
        ];
    }
}
