<?php

namespace Tests\Feature\Listeners;

use App\Listeners\RecordLastLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RecordLastLoginListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_last_login_timestamp_and_ip(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 14:30:00', 'UTC'));

        $user = \App\Models\User::factory()->create([
            'last_login_at' => null,
        ]);

        $listener = new RecordLastLogin;
        $listener->handle(new Login('web', $user, false));

        $user->refresh();

        $this->assertNotNull($user->last_login_at);
        $this->assertTrue($user->last_login_at->equalTo(Carbon::parse('2026-03-15 14:30:00', 'UTC')));
        $this->assertNotNull($user->last_login_ip);

        Carbon::setTestNow();
    }
}
