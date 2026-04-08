<?php

namespace Tests\Unit\Models;

use App\Models\PlatformSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformSettingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_value_casts_by_type(): void
    {
        PlatformSetting::query()->create(['key' => 'x_int', 'value' => '7', 'type' => 'integer']);
        PlatformSetting::query()->create(['key' => 'x_bool', 'value' => '0', 'type' => 'boolean']);

        $this->assertSame(7, PlatformSetting::getValue('x_int'));
        $this->assertFalse(PlatformSetting::getValue('x_bool'));
    }

    public function test_get_value_returns_default_when_missing(): void
    {
        $this->assertSame('d', PlatformSetting::getValue('missing_key', 'd'));
    }
}
