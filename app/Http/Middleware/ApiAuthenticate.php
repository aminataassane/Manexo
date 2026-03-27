<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Non authentifié.'], 401);
        }

        $token = $user->currentAccessToken();

        if (! $token) {
            return response()->json(['error' => 'Token invalide.'], 401);
        }

        // Check token expiration
        if ($token->expires_at && $token->expires_at->isPast()) {
            return response()->json(['error' => 'Token expiré.'], 401);
        }

        $orgId = $token->organization_id ?? null;

        if (! $orgId) {
            return response()->json(['error' => 'Token non lié à une organisation.'], 403);
        }

        // Check organization exists
        $org = \App\Models\Organization::find($orgId);
        if (! $org) {
            return response()->json(['error' => 'Organisation introuvable.'], 403);
        }

        // Check user is member of organization
        $isMember = $user->organizations()->where('organization_id', $orgId)->exists();
        if (! $isMember) {
            return response()->json(['error' => 'Accès refusé à cette organisation.'], 403);
        }

        // Set session for BelongsToOrganization scope and OrganizationAuditService
        session(['current_organization_id' => $orgId]);

        // Set request attributes for downstream use
        $request->attributes->set('apiOrganizationId', $orgId);
        $scopes = $token->scopes ?? $token->abilities ?? [];
        if (is_string($scopes)) {
            $scopes = json_decode($scopes, true) ?? [];
        }
        $request->attributes->set('apiTokenScopes', $scopes);

        return $next($request);
    }
}
