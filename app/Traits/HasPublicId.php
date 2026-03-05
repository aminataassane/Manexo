<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Adds a prefixed ULID `public_id` to models exposed in URLs.
 *
 * Usage: `use HasPublicId;` + `public static string $publicIdPrefix = 'TCK';`
 * Adds `public_id` to $fillable manually in each model.
 */
trait HasPublicId
{
    public static function bootHasPublicId(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->public_id)) {
                $model->public_id = static::generatePublicId();
            }
        });
    }

    public static function generatePublicId(): string
    {
        return static::$publicIdPrefix . '-' . Str::ulid()->toBase32();
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    /**
     * Optimised route model binding query on the indexed public_id column.
     */
    public function resolveRouteBindingQuery($query, $value, $field = null): Builder
    {
        return $query->where($field ?? 'public_id', $value);
    }

    /**
     * Short reference for UI display: PREFIX-8CHARS (e.g. TCK-01J9ZK5R).
     */
    public function shortReference(): string
    {
        $parts = explode('-', $this->public_id ?? '', 2);

        return isset($parts[1])
            ? $parts[0] . '-' . substr($parts[1], 0, 8)
            : ($this->public_id ?? '');
    }
}
