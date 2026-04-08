<?php

namespace Tests\Unit\Http;

use App\Http\Middleware\DecodeApiToken;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class DecodeApiTokenMiddlewareTest extends TestCase
{
    public function test_decodes_mnx_prefixed_base64_to_sanctum_bearer_format(): void
    {
        $inner = '1|sanctum-token-part';
        $header = 'Bearer mnx_'.base64_encode($inner);

        $request = Request::create('/api/v1/ping', 'GET', [], [], [], [
            'HTTP_AUTHORIZATION' => $header,
        ]);

        $middleware = new DecodeApiToken;
        $middleware->handle($request, function (Request $req) use ($inner): Response {
            $this->assertSame('Bearer '.$inner, $req->header('Authorization'));

            return new Response('ok');
        });
    }

    public function test_passes_through_non_mnx_bearer_unchanged(): void
    {
        $request = Request::create('/api/v1/ping', 'GET', [], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer 1|already-plain',
        ]);

        $middleware = new DecodeApiToken;
        $middleware->handle($request, function (Request $req): Response {
            $this->assertSame('Bearer 1|already-plain', $req->header('Authorization'));

            return new Response('ok');
        });
    }
}
