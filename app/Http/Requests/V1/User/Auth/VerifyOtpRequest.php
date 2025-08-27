<?php

namespace App\Http\Requests\V1\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'verification_token' => ['required', 'max:255'],
            'otp' => ['required', 'digits:6'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'verification_token.required' => __('validation.custom.verification_token.required'),
            'verification_token.max' => __('validation.max.string', ['attribute' => __('validation.attributes.verification_token'), 'max' => 255]),
            'otp.required' => __('validation.custom.otp_code.required'),
            'otp.digits' => __('validation.custom.otp_code.digits'),
        ];
    }
}
