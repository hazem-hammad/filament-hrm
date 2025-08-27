<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleFavoriteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'property_id' => 'required|integer|exists:properties,id',
        ];
    }
}
