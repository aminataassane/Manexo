<?php

namespace App\Http\Middleware;

use App\Services\PlatformSettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Enforce2FA
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Skip 2FA setup/challenge routes and logout
        if ($request->routeIs('two-factor.*', 'logout', 'profile')) {
            return $next($request);
        }

        // Check if 2FA challenge is pending (user logged in with password but hasn't verified 2FA code)
        if (session('2fa_pending') && $user->two_factor_secret && $user->two_factor_confirmed_at) {
            return redirect()->route('two-factor.challenge');
        }

        // Check if 2FA is required for admins
        $settings = app(PlatformSettingsService::class);
        $force2faForAdmins = (bool) $settings->get('force_2fa_for_admins', false);

        if (! $force2faForAdmins) {
            return $next($request);
        }

        // Determine if user is an admin (org-level or platform-level)
        $isAdmin = $user->hasPlatformAccess();

        if (! $isAdmin) {
            $orgId = (int) session('current_organization_id');
            if ($orgId) {
                $role = $user->orgRole($orgId);
                $isAdmin = in_array($role, ['owner', 'admin'], true);
            }
        }

        // Admin without 2FA configured → redirect to setup
        if ($isAdmin && ! $user->two_factor_confirmed_at) {
            return redirect()->route('two-factor.setup')
                ->with('2fa_required', true);
        }

        return $next($request);
    }
}
