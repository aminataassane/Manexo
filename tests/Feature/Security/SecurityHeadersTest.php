<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_security_headers_present_on_response(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_hsts_not_set_in_testing_environment(): void
    {
        $response = $this->get('/');

        // HSTS should only be set in production
        $response->assertHeaderMissing('Strict-Transport-Security');
    }
}
