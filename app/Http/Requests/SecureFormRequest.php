<?php

namespace App\Http\Requests;

use App\Traits\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

abstract class SecureFormRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Get sanitized validated data
     */
    public function sanitized(): array
    {
        return $this->sanitizeInput($this->validated());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input before validation
        $input = $this->all();
        $sanitized = $this->sanitizeInput($input);
        $this->replace($sanitized);
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'url' => 'The :attribute must be a valid URL.',
            'numeric' => 'The :attribute must be a number.',
            'integer' => 'The :attribute must be an integer.',
            'boolean' => 'The :attribute must be true or false.',
            'date' => 'The :attribute must be a valid date.',
            'max' => 'The :attribute must not be greater than :max characters.',
            'min' => 'The :attribute must be at least :min characters.',
            'regex' => 'The :attribute format is invalid.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'file' => 'The :attribute must be a file.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->performAdditionalValidation($validator);
        });
    }

    /**
     * Perform additional validation checks
     */
    protected function performAdditionalValidation($validator): void
    {
        // Check for suspicious patterns in all string inputs
        foreach ($this->all() as $key => $value) {
            if (is_string($value) && $this->containsSuspiciousPatterns($value)) {
                $validator->errors()->add($key, 'The ' . $key . ' contains invalid characters.');
            }
        }
    }
}