<?php

namespace App\Http\Requests;

use Doctrine\Inflector\Rules\English\Rules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
            'hour_rate' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'bio' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->route('id')) {
            $this->merge(['id' => $this->route('id')]);
        }
    }
}
