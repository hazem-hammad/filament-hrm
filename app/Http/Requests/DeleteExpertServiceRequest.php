<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteExpertServiceRequest extends FormRequest
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
            'id' => ['required', 'integer', Rule::exists('expert_services', 'service_id')->where('user_id', auth('api')->id())],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->route('id')) {
            $this->merge(['id' => $this->route('id')]);
        }
    }
}
