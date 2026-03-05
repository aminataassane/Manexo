<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => (bool) $setting->value,
            'float' => (float) $setting->value,
            default => $setting->value,
        };
    }

    public static function setValue(string $key, mixed $value, string $type = 'string'): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'type' => $type],
        );
    }
}
