<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationIsSelected
{
    /**
     * Ensure the authenticated user has an active organization selected.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Prevent redirect loops.
        if ($request->routeIs('organizations.*')) {
            return $next($request);
        }

        $currentId = $request->session()->get('current_organization_id');

        if ($currentId) {
            $org = $user->organizations()->whereKey($currentId)->first();

            if ($org) {
                $request->attributes->set('currentOrganization', $org);
                view()->share('currentOrganization', $org);

                return $next($request);
            }

            // Selected org no longer accessible
            $request->session()->forget('current_organization_id');
        }

        // Auto-select if the user belongs to exactly one organization
        $orgCount = $user->organizations()->count();
        if ($orgCount === 1) {
            $org = $user->organizations()->first();

            if ($org) {
                $request->session()->put('current_organization_id', $org->id);
                $request->attributes->set('currentOrganization', $org);
                view()->share('currentOrganization', $org);

                return $next($request);
            }
        }

        return redirect()->route('organizations.select');
    }
}

