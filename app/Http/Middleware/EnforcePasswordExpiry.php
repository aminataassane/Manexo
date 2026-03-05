<?php

namespace App\Http\Middleware;

use App\Services\PlatformSettingsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class EnforcePasswordExpiry
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Skip for platform admin routes and password change routes
        if ($request->routeIs('password.*', 'profile', 'logout')) {
            return $next($request);
        }

        $settings = app(PlatformSettingsService::class);
        $expiryDays = (int) $settings->get('password_expiration_days', 0);

        if ($expiryDays <= 0) {
            return $next($request);
        }

        $passwordChangedAt = $user->password_changed_at ?? $user->created_at;

        if ($passwordChangedAt instanceof Carbon && $passwordChangedAt->addDays($expiryDays)->isPast()) {
            return redirect()->route('profile')
                ->with('password_expired', true);
        }

        return $next($request);
    }
}
