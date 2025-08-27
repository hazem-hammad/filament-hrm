<?php

namespace App\Http\Requests\V1\User\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => [
                'nullable',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z\s\'\-]+$/',
            ],
            'last_name' => [
                'nullable',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z\s\'\-]+$/',
            ],
            'bio' => ['nullable', 'string', 'min:2', 'max:255'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'image' => ['nullable', 'ends_with:jpg,jpeg,png,webp', 'max:2048'],
            'nationality_id' => ['nullable', 'exists:nationalities,id'],
            'languages' => ['nullable', 'array', 'exists:languages,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.regex' => __('The first name may only contain letters, spaces, apostrophes, and hyphens.'),
            'first_name.min' => __('The first name must be at least 2 characters long.'),
            'first_name.max' => __('The first name may not be greater than 50 characters.'),
            'last_name.regex' => __('The last name may only contain letters, spaces, apostrophes, and hyphens.'),
            'last_name.min' => __('The last name must be at least 2 characters long.'),
            'last_name.max' => __('The last name may not be greater than 50 characters.'),
            'bio.min' => __('The bio must be at least 2 characters long.'),
            'bio.max' => __('The bio may not be greater than 255 characters.'),
            'birthdate.date' => __('The birthdate must be a valid date.'),
            'birthdate.before' => __('The birthdate must be before today.'),
            'image.ends_with' => __('The image must be a file of type: jpg, jpeg, png, webp.'),
            'image.max' => __('The image may not be greater than 2MB.'),
            'nationality_id.exists' => __('The selected nationality is invalid.'),
            'languages.array' => __('The languages must be an array.'),
            'languages.exists' => __('The selected languages are invalid.'),
        ];
    }
}
