<?php

namespace App\Http\Requests\V1\User\Service;

use App\Enum\StatusEnum;
use App\Enum\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListServiceRequest extends FormRequest
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
            'filter' => ['array', 'nullable'],
            'filter.expert_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('status', StatusEnum::ACTIVE->value)->where('type', UserType::EXPERT->value)],
            'filter.category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('status', StatusEnum::ACTIVE->value)],
            'filter.is_featured' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
