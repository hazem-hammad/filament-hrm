<?php

namespace App\Http\Requests;

use App\Enum\StatusEnum;
use App\Rules\UserIsExpert;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class storeServiceRequest extends FormRequest
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
            'services' => ['required', 'array', 'min:1', 'max:50', new UserIsExpert],
            'services.*.service_id' => ['required', 'integer',
                Rule::exists('services', 'id')->where('status', StatusEnum::ACTIVE->value),
                Rule::unique('expert_services', 'service_id')
                    ->where(function ($query) {
                        $query->where('user_id', auth('api')->id());
                    }),
            ],
            'services.*.hour_rate' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'services.*.bio' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages()
    {
        return [
            'services.*.service_id.unique' => __('This service is already assigned to you.'),
            'services.*.service_id.exists' => __('The selected service does not exist or is inactive.'),
        ];
    }
}
