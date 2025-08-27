<?php

namespace App\Http\Requests\V1\Banner;

use App\Http\Requests\V1\User\Common\PaginationListRequest;

final class ListBannerRequest extends PaginationListRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'sort_by' => 'nullable|string|in:sort_order,created_at',
            'sort_order' => 'nullable|string|in:asc,desc',
            'paginated' => 'nullable|boolean',
        ]);
    }
}