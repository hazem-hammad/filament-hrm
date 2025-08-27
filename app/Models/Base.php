<?php

namespace App\Models;

use App\Enum\StatusEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 *
 * @mixin Builder
 */
class Base extends Model
{
    /**
     * @var array Allowed fields for search
     */
    protected static array $allowedSearchFields = [];

    /**
     * @var array Allowed fields for sorting
     */
    protected static array $allowedSortFields = ['created_at'];

    /**
     * Get the allowed search fields.
     */
    public static function getAllowedSearchFields(): array
    {
        return static::$allowedSearchFields;
    }

    /**
     * Get the allowed sort fields.
     */
    public static function getAllowedSortFields(): array
    {
        return static::$allowedSortFields;
    }

    /**
     * Apply all relevant Sizes filters
     */
    #[Scope]
    public function active(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }

    /**
     * Apply the relevant filters to the query.
     */
    #[Scope]
    public function filter(Builder $query): Builder
    {
        $filterClass = str_replace('Models', 'Filters', get_class($this)).'Filters';

        return (new $filterClass(request: request(), builder: $query))->apply();
    }
}
