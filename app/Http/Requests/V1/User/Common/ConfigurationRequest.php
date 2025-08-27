<?php

namespace App\Http\Requests\V1\User\Common;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
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
            'platform' => 'required|string|in:ios,android',
            'version' => 'required',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'platform' => strtolower(request()->header('Platform')),
            'version' => request()->header('Version'),
        ]);
    }
}
