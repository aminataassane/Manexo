<?php

namespace Tests\Unit\Services;

use App\Models\Organization;
use App\Services\BusinessHoursService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BusinessHoursServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_business_minutes_when_disabled_equals_plain_addition(): void
    {
        Cache::flush();
        $org = Organization::factory()->create([
            'settings' => [
                'sla' => [
                    'business_hours' => [
                        'enabled' => false,
                    ],
                ],
            ],
        ]);

        $start = Carbon::parse('2026-06-02 10:00:00', 'UTC');
        $end = BusinessHoursService::addBusinessMinutes($start, 90, (int) $org->id);

        $this->assertTrue($end->equalTo($start->copy()->addMinutes(90)));
    }

    public function test_get_config_returns_defaults_when_no_sla_settings(): void
    {
        Cache::flush();
        $org = Organization::factory()->create(['settings' => []]);

        $config = BusinessHoursService::getConfig((int) $org->id);

        $this->assertFalse($config['enabled']);
        $this->assertSame([1, 2, 3, 4, 5], $config['working_days']);
        $this->assertSame('09:00', $config['start']);
        $this->assertSame('18:00', $config['end']);
    }
}
