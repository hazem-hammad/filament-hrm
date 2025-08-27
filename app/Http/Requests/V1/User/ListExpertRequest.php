<?php

namespace App\Http\Requests\V1\User;

use App\Enum\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListExpertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter.min_price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'filter.max_price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'filter.service_id' => ['nullable', 'array'],
            'filter.service_id.*' => ['integer', Rule::exists('services', 'id')->where('status', StatusEnum::ACTIVE->value)],
            'filter.category_id' => ['nullable', 'array'],
            'filter.category_id.*' => ['integer', Rule::exists('categories', 'id')->where('status', StatusEnum::ACTIVE->value)],
            'filter.is_featured' => ['nullable', 'boolean'],
            'filter.is_favorite' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
