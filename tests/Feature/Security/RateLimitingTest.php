<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    public function test_password_reset_is_rate_limited(): void
    {
        // Make 3 requests (the limit)
        for ($i = 0; $i < 3; $i++) {
            $this->get('/forgot-password');
        }

        // 4th request should be rate limited
        $response = $this->get('/forgot-password');
        $response->assertStatus(429);
    }

    public function test_platform_login_is_rate_limited(): void
    {
        // Make 5 requests (the limit)
        for ($i = 0; $i < 5; $i++) {
            $this->get('/platform-admin/login');
        }

        // 6th request should be rate limited
        $response = $this->get('/platform-admin/login');
        $response->assertStatus(429);
    }
}
