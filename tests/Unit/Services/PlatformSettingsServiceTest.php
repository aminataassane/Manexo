<?php

namespace Tests\Unit\Services;

use App\Models\PlatformSetting;
use App\Services\PlatformSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PlatformSettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_casts_integer_boolean_and_string(): void
    {
        Cache::flush();

        PlatformSetting::query()->create(['key' => 'k_int', 'value' => '42', 'type' => 'integer']);
        PlatformSetting::query()->create(['key' => 'k_bool', 'value' => '1', 'type' => 'boolean']);
        PlatformSetting::query()->create(['key' => 'k_str', 'value' => 'hello', 'type' => 'string']);

        $svc = new PlatformSettingsService;

        $this->assertSame(42, $svc->get('k_int'));
        $this->assertTrue($svc->get('k_bool'));
        $this->assertSame('hello', $svc->get('k_str'));
    }

    public function test_get_returns_default_when_key_missing(): void
    {
        Cache::flush();

        $svc = new PlatformSettingsService;

        $this->assertSame('fallback', $svc->get('does_not_exist', 'fallback'));
    }
}
