<?php

namespace App\Http\Requests\V1\User\Information;

use Illuminate\Foundation\Http\FormRequest;

class GetPageBySlugRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => [
                'required',
                'exists:pages,slug,status,' . \App\Enum\StatusEnum::ACTIVE->value
            ],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->route('slug'),
        ]);
    }
}
