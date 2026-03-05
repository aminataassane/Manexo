<?php

namespace App\Services;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Cache;

class PlatformSettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        if (! isset($all[$key])) {
            return $default;
        }

        $setting = $all[$key];

        return match ($setting['type']) {
            'integer' => (int) $setting['value'],
            'boolean' => (bool) $setting['value'],
            'float' => (float) $setting['value'],
            default => $setting['value'],
        };
    }

    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        PlatformSetting::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'type' => $type],
        );

        Cache::forget('platform_settings_all');
    }

    public function all(): array
    {
        return Cache::remember('platform_settings_all', 3600, function () {
            return PlatformSetting::all()
                ->keyBy('key')
                ->map(fn ($s) => ['value' => $s->value, 'type' => $s->type])
                ->toArray();
        });
    }
}
