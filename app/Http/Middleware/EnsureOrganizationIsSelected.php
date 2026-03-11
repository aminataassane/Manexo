<?php

namespace App\Http\Middleware;

use App\Models\SupportSession;
use App\Services\SuperAdminAuditService;
use App\Services\SupportSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationIsSelected
{
    /**
     * Ensure the authenticated user has an active organization selected.
     *
     * Caches the org lookup per request cycle to avoid repeated DB queries
     * (especially important for Livewire persistent middleware).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Prevent redirect loops & allow profile without an org.
        if ($request->routeIs('organizations.*', 'profile', 'profile.*')) {
            return $next($request);
        }

        // Already resolved in this request cycle (Livewire re-entrance)
        if ($request->attributes->get('currentOrganization')) {
            return $next($request);
        }

        // Check support session for platform admins (super_admin + platform_admin)
        if ($user->canPlatformManage()) {
            $supportSessionId = $request->session()->get('support_session_id');

            if ($supportSessionId) {
                $session = SupportSession::with('organization')->find($supportSessionId);

                if ($session) {
                    $service = new SupportSessionService;

                    if ($service->checkExpiry($session)) {
                        // Session expired
                        SuperAdminAuditService::log('support_session.expired', 'Organization', $session->organization_id, [
                            'session_id' => $session->id,
                            'org_name' => $session->organization?->name,
                        ]);

                        $request->session()->forget('current_organization_id');
                        $request->session()->forget('support_session_id');

                        return redirect()->route('platform-admin.support-sessions')
                            ->with('error', __('super_admin.support.expired'));
                    }

                    if ($session->isActive()) {
                        view()->share('activeSupportSession', $session);
                    }
                }
            }
        }

        $currentId = $request->session()->get('current_organization_id');

        if ($currentId) {
            // Platform admins (super_admin + platform_admin) can access any active org without membership
            if ($user->canPlatformManage()) {
                $org = Cache::remember("sa_org:{$currentId}", 300, fn () =>
                    \App\Models\Organization::find($currentId)
                );
                if ($org && $org->isActive()) {
                    $request->attributes->set('currentOrganization', $org);
                    view()->share('currentOrganization', $org);
                    return $next($request);
                }
            }

            // Cache per user+org for 5 min to avoid querying on every Livewire update
            $cacheKey = "user_org:{$user->id}:{$currentId}";
            $org = Cache::remember($cacheKey, 300, function () use ($user, $currentId) {
                return $user->organizations()->whereKey($currentId)->first();
            });

            if ($org) {
                if (! $org->isActive()) {
                    Cache::forget($cacheKey);
                    $request->session()->forget('current_organization_id');

                    return redirect()->route('organizations.select')
                        ->with('error', __('super_admin.org_not_active'));
                }

                $request->attributes->set('currentOrganization', $org);
                view()->share('currentOrganization', $org);

                return $next($request);
            }

            // Selected org no longer accessible
            Cache::forget($cacheKey);
            $request->session()->forget('current_organization_id');
        }

        // Auto-select if the user belongs to exactly one organization
        $orgs = Cache::remember("user_orgs:{$user->id}", 300, function () use ($user) {
            return $user->organizations()->limit(2)->get();
        });

        if ($orgs->count() === 1) {
            $org = $orgs->first();
            $request->session()->put('current_organization_id', $org->id);
            $request->attributes->set('currentOrganization', $org);
            view()->share('currentOrganization', $org);

            return $next($request);
        }

        return redirect()->route('organizations.select');
    }
}
