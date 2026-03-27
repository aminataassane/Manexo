<?php

namespace App\Http\Middleware;

use App\Services\PlatformSettingsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnforceSessionLimits
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $settings = app(PlatformSettingsService::class);
        $maxSessions = (int) $settings->get('max_concurrent_sessions', 0);

        if ($maxSessions <= 0) {
            return $next($request);
        }

        // Only check every 2 minutes per user to avoid a DB query on every request
        $cacheKey = "session_limit_checked:{$user->id}";
        if (Cache::has($cacheKey)) {
            return $next($request);
        }

        $currentSessionId = $request->session()->getId();

        // Count active sessions for this user
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get(['id', 'last_activity']);

        if ($sessions->count() > $maxSessions) {
            // Keep the most recent sessions (including current), remove the rest
            $sessionsToKeep = $sessions
                ->sortByDesc('last_activity')
                ->take($maxSessions)
                ->pluck('id')
                ->push($currentSessionId)
                ->unique();

            DB::table('sessions')
                ->where('user_id', $user->id)
                ->whereNotIn('id', $sessionsToKeep->all())
                ->delete();
        }

        Cache::put($cacheKey, true, 600); // 10 min TTL

        return $next($request);
    }
}
