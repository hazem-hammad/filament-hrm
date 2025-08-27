<?php

namespace App\Http\Requests\V1\User\Common;

use Illuminate\Foundation\Http\FormRequest;

class PaginationListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get default values for pagination.
     */
    public function getPage(): int
    {
        return $this->input('page', 1);
    }

    /**
     * Get the number of items per page.
     */
    public function getLimit(): int
    {
        return $this->input('limit', 10);
    }

    /**
     * Get validated data as DTO.
     */
    public function toDTO(): array
    {
        return [
            'page' => $this->getPage(),
            'limit' => $this->getLimit(),
            'search' => $this->input('search'),
        ];
    }
}
