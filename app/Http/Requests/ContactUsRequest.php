<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('validation.attributes.name')]),
            'name.string' => __('validation.string', ['attribute' => __('validation.attributes.name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name'), 'max' => 255]),
            'email.required' => __('validation.required', ['attribute' => __('validation.attributes.email')]),
            'email.email' => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'email.max' => __('validation.max.string', ['attribute' => __('validation.attributes.email'), 'max' => 255]),
            'subject.required' => __('validation.required', ['attribute' => __('validation.attributes.subject')]),
            'subject.string' => __('validation.string', ['attribute' => __('validation.attributes.subject')]),
            'subject.max' => __('validation.max.string', ['attribute' => __('validation.attributes.subject'), 'max' => 255]),
            'message.required' => __('validation.required', ['attribute' => __('validation.attributes.message')]),
            'message.string' => __('validation.string', ['attribute' => __('validation.attributes.message')]),
            'message.max' => __('validation.max.string', ['attribute' => __('validation.attributes.message'), 'max' => 1000]),
        ];
    }
}
