<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Remove server fingerprint headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content-Security-Policy
        $reverbHost = (string) config('broadcasting.connections.reverb.options.host', '');
        $reverbPort = (string) config('broadcasting.connections.reverb.options.port', '');
        $wsConnect = '';
        if ($reverbHost !== '') {
            $hostPort = $reverbHost . ($reverbPort !== '' ? ':' . $reverbPort : '');
            // wss:// for production, ws:// for local dev (no TLS)
            $wsConnect = ' wss://' . $hostPort . ' ws://' . $hostPort;
        }

        // Livewire injects inline <script> tags and Alpine.js evaluates
        // x-data / x-init expressions at runtime, so 'unsafe-inline' and
        // 'unsafe-eval' are required for script-src with this stack.
        // The CSP still locks down allowed domains, frame embedding,
        // base-uri and form-action targets.
        $viteDev = app()->environment('local')
            ? ' http://127.0.0.1:5173 ws://127.0.0.1:5173 http://localhost:5173 ws://localhost:5173'
            : '';

        $workerSrc = app()->environment('local') ? "worker-src 'self' blob:" : "worker-src 'self'";

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.iconify.design" . $viteDev,
            $workerSrc,
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com" . $viteDev,
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data:",
            "connect-src 'self' https://api.iconify.design" . $wsConnect . $viteDev,
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
