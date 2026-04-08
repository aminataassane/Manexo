<?php

namespace App\Services;

use App\Models\Form;
use App\Models\Organization;
use App\Models\OrganizationFunction;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Support\Facades\Cache;

class OnboardingService
{
    /**
     * Ordered list of onboarding step keys — follows the implementation doc priority.
     *
     * Phase 1 (high impact, low complexity):
     *   categories, priorities, functions, invite_team
     * Phase 2 (once flow is running):
     *   customize, create_form, first_test
     */
    public const STEPS = [
        'categories',
        'priorities',
        'functions',
        'invite_team',
        'customize',
        'create_form',
        'first_test',
    ];

    /**
     * Get the full onboarding state for an organization.
     *
     * @return array<string, array{completed: bool, route: string|null, route_params?: array<string, string>}>
     */
    public static function state(Organization $org): array
    {
        $orgId = (int) $org->id;

        return Cache::remember("onboarding_state:v2:{$orgId}", 60, function () use ($org, $orgId) {
            return [
                'categories' => [
                    'completed' => self::hasCategories($orgId),
                    'route' => 'admin.settings',
                    'route_params' => ['tab' => 'categories'],
                ],
                'priorities' => [
                    'completed' => self::hasPriorities($orgId),
                    'route' => 'admin.settings',
                    'route_params' => ['tab' => 'priorities'],
                ],
                'functions' => [
                    'completed' => self::hasFunctions($orgId),
                    'route' => 'admin.settings',
                    'route_params' => ['tab' => 'functions'],
                ],
                'invite_team' => [
                    'completed' => self::hasTeamMembers($orgId),
                    'route' => 'admin.users',
                ],
                'customize' => [
                    'completed' => self::isCustomized($org),
                    'route' => 'admin.settings',
                    'route_params' => ['tab' => 'branding'],
                ],
                'create_form' => [
                    'completed' => self::hasForm($orgId),
                    'route' => 'admin.forms',
                ],
                'first_test' => [
                    'completed' => self::hasTicket($orgId),
                    'route' => 'tickets.create',
                ],
            ];
        });
    }

    /**
     * Count completed steps.
     */
    public static function completedCount(Organization $org): int
    {
        return collect(self::state($org))->where('completed', true)->count();
    }

    /**
     * Check if all steps are completed.
     */
    public static function isComplete(Organization $org): bool
    {
        return self::completedCount($org) === count(self::STEPS);
    }

    /**
     * Check if the onboarding has been dismissed by the user.
     */
    public static function isDismissedByOrg(Organization $org): bool
    {
        return (bool) ($org->settings['onboarding_dismissed'] ?? false);
    }

    /**
     * Dismiss the onboarding checklist.
     */
    public static function dismiss(Organization $org): void
    {
        $settings = $org->settings ?? [];
        $settings['onboarding_dismissed'] = true;
        $org->update(['settings' => $settings]);
        self::clearCache((int) $org->id);
    }

    /**
     * Mark a specific step as manually completed (for steps that can't be auto-detected).
     */
    public static function markStepDone(Organization $org, string $step): void
    {
        $settings = $org->settings ?? [];
        $settings['onboarding_done'][$step] = true;
        $org->update(['settings' => $settings]);
        self::clearCache((int) $org->id);
    }

    public static function clearCache(int $orgId): void
    {
        Cache::forget("onboarding_state:{$orgId}");
        Cache::forget("onboarding_state:v2:{$orgId}");
    }

    // ── Detection helpers ──

    private static function hasCategories(int $orgId): bool
    {
        return TicketCategory::where('organization_id', $orgId)->exists();
    }

    private static function hasPriorities(int $orgId): bool
    {
        return TicketPriority::where('organization_id', $orgId)->exists();
    }

    private static function hasFunctions(int $orgId): bool
    {
        return OrganizationFunction::where('organization_id', $orgId)->exists();
    }

    private static function hasTeamMembers(int $orgId): bool
    {
        return OrganizationMembership::where('organization_id', $orgId)->count() > 1;
    }

    private static function isCustomized(Organization $org): bool
    {
        return $org->logo_path !== null || $org->primary_color !== null;
    }

    private static function hasForm(int $orgId): bool
    {
        return Form::where('organization_id', $orgId)->exists();
    }

    private static function hasTicket(int $orgId): bool
    {
        return Ticket::where('organization_id', $orgId)->exists();
    }
}
