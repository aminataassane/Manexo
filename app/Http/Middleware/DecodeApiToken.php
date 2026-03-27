<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Decodes Manexo API tokens (mnx_...) back to Sanctum format before auth.
 *
 * Tokens are displayed to users as "mnx_{base64}" to hide the internal
 * Sanctum "{id}|{hash}" format. This middleware transparently decodes
 * them so Sanctum can authenticate normally.
 */
class DecodeApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (str_starts_with($header, 'Bearer mnx_')) {
            $encoded = substr($header, strlen('Bearer mnx_'));
            $decoded = base64_decode($encoded, true);

            if ($decoded !== false && str_contains($decoded, '|')) {
                $request->headers->set('Authorization', 'Bearer '.$decoded);
            }
        } elseif (str_starts_with($header, 'Bearer ') && ! str_contains($header, '|')) {
            // Token sent without mnx_ prefix and without pipe — might be raw base64
            $raw = substr($header, 7);
            $decoded = base64_decode($raw, true);
            if ($decoded !== false && str_contains($decoded, '|')) {
                $request->headers->set('Authorization', 'Bearer '.$decoded);
            }
        }

        return $next($request);
    }
}
