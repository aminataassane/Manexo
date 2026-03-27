<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCheckScope
{
    public function handle(Request $request, Closure $next, string ...$requiredScopes): Response
    {
        $tokenScopes = $request->attributes->get('apiTokenScopes', []);

        // Handle double-encoded JSON scopes (string instead of array)
        if (is_string($tokenScopes)) {
            $tokenScopes = json_decode($tokenScopes, true) ?? [];
        }

        foreach ($requiredScopes as $scope) {
            if (! in_array($scope, $tokenScopes, true)) {
                return response()->json([
                    'error' => "Scope manquant: {$scope}",
                ], 403);
            }
        }

        return $next($request);
    }
}
