<?php

namespace Tests\Unit\Http;

use App\Http\Middleware\ApiCheckScope;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApiCheckScopeTest extends TestCase
{
    public function test_allows_request_when_all_required_scopes_present(): void
    {
        $middleware = new ApiCheckScope;
        $request = Request::create('/api/v1/tickets', 'GET');
        $request->attributes->set('apiTokenScopes', ['tickets:read', 'tickets:write']);

        $called = false;
        $next = function (Request $req) use (&$called): Response {
            $called = true;

            return new Response('ok', 200);
        };

        $response = $middleware->handle($request, $next, 'tickets:read');

        $this->assertTrue($called);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_rejects_when_scope_missing(): void
    {
        $middleware = new ApiCheckScope;
        $request = Request::create('/api/v1/tickets', 'POST');
        $request->attributes->set('apiTokenScopes', ['tickets:read']);

        $response = $middleware->handle($request, fn () => new Response('ok'), 'tickets:write');

        $this->assertSame(403, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('tickets:write', (string) $data['error']);
    }

    public function test_decodes_string_encoded_json_scopes(): void
    {
        $middleware = new ApiCheckScope;
        $request = Request::create('/api/ping', 'GET');
        $request->attributes->set('apiTokenScopes', '["comments:read"]');

        $called = false;
        $response = $middleware->handle($request, function () use (&$called) {
            $called = true;

            return new Response('ok');
        }, 'comments:read');

        $this->assertTrue($called);
        $this->assertSame(200, $response->getStatusCode());
    }
}
