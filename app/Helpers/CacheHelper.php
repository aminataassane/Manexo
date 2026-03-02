<?php

namespace App\Helpers;

use App\Models\RoleDefinition;
use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /** Cache TTL in seconds (30 minutes). */
    public const TTL = 1800;

    /** Short TTL for notification counts (1 minute). */
    public const TTL_SHORT = 60;

    // ─── Key generators ──────────────────────────────────────────────

    public static function dashboardKpisKey(int $orgId): string
    {
        return "dashboard:kpis:{$orgId}";
    }

    public static function dashboardChartKey(int $orgId, int $days): string
    {
        return "dashboard:chart:{$orgId}:{$days}";
    }

    public static function dashboardPriorityTicketsKey(int $orgId): string
    {
        return "dashboard:priority_tickets:{$orgId}";
    }

    public static function dashboardRecentDiscussionsKey(int $orgId): string
    {
        return "dashboard:recent_discussions:{$orgId}";
    }

    public static function dashboardDiscussionsUnreadKey(int $orgId): string
    {
        return "dashboard:discussions_unread:{$orgId}";
    }

    public static function dashboardRecentActivityKey(int $orgId): string
    {
        return "dashboard:recent_activity:{$orgId}";
    }

    public static function dashboardPendingFormsKey(int $orgId, int $userId): string
    {
        return "dashboard:pending_forms:{$orgId}:{$userId}";
    }

    public static function dashboardPendingFormsCountKey(int $orgId, int $userId): string
    {
        return "dashboard:pending_forms_count:{$orgId}:{$userId}";
    }

    public static function categoriesKey(int $orgId, bool $activeOnly): string
    {
        $variant = $activeOnly ? 'active' : 'all';

        return "categories:{$orgId}:{$variant}";
    }

    public static function prioritiesKey(int $orgId, bool $activeOnly): string
    {
        $variant = $activeOnly ? 'active' : 'all';

        return "priorities:{$orgId}:{$variant}";
    }

    public static function membersKey(int $orgId): string
    {
        return "members:{$orgId}";
    }

    public static function orgFunctionsKey(int $orgId): string
    {
        return "org_functions:{$orgId}";
    }

    public static function formsListKey(int $orgId): string
    {
        return "forms_list:{$orgId}";
    }

    public static function reportsKey(int $orgId, string $period): string
    {
        return "reports:{$orgId}:{$period}";
    }

    /** Notifications: unread count (topbar bell). */
    public static function notificationsUnreadCountKey(int $userId): string
    {
        return "notifications:unread_count:{$userId}";
    }

    /** Sidebar: discussions unread badge (DiscussionNewMessage + DiscussionInvite only). */
    public static function sidebarDiscussionsUnreadKey(int $userId): string
    {
        return "sidebar:discussions_unread:{$userId}";
    }

    /** Permissions for a specific org + role combination. */
    public static function rolePermissionsKey(int $orgId, string $role): string
    {
        return "org_perms:{$orgId}:{$role}";
    }

    // ─── Invalidation ────────────────────────────────────────────────

    public static function invalidateDashboard(int $orgId): void
    {
        Cache::forget(self::dashboardKpisKey($orgId));
        Cache::forget(self::dashboardChartKey($orgId, 7));
        Cache::forget(self::dashboardChartKey($orgId, 30));
        Cache::forget(self::dashboardPriorityTicketsKey($orgId));
        Cache::forget(self::dashboardRecentActivityKey($orgId));
    }

    public static function invalidateDashboardDiscussions(int $orgId): void
    {
        Cache::forget(self::dashboardRecentDiscussionsKey($orgId));
        Cache::forget(self::dashboardDiscussionsUnreadKey($orgId));
    }

    public static function invalidatePendingForms(int $orgId, int $userId): void
    {
        Cache::forget(self::dashboardPendingFormsKey($orgId, $userId));
        Cache::forget(self::dashboardPendingFormsCountKey($orgId, $userId));
    }

    public static function invalidateCategories(int $orgId): void
    {
        Cache::forget(self::categoriesKey($orgId, true));
        Cache::forget(self::categoriesKey($orgId, false));
    }

    public static function invalidatePriorities(int $orgId): void
    {
        Cache::forget(self::prioritiesKey($orgId, true));
        Cache::forget(self::prioritiesKey($orgId, false));
    }

    public static function invalidateMembers(int $orgId): void
    {
        Cache::forget(self::membersKey($orgId));
    }

    public static function invalidateOrgFunctions(int $orgId): void
    {
        Cache::forget(self::orgFunctionsKey($orgId));
    }

    public static function invalidateForms(int $orgId): void
    {
        Cache::forget(self::formsListKey($orgId));
    }

    public static function invalidateReports(int $orgId): void
    {
        Cache::forget(self::reportsKey($orgId, 'default'));
        Cache::forget(self::reportsKey($orgId, 'monthly'));
        Cache::forget(self::reportsKey($orgId, 'yearly'));
    }

    public static function invalidateNotificationsCount(int $userId): void
    {
        Cache::forget(self::notificationsUnreadCountKey($userId));
    }

    public static function invalidateSidebarDiscussionsUnread(int $userId): void
    {
        Cache::forget(self::sidebarDiscussionsUnreadKey($userId));
    }

    public static function invalidateRolePermissions(int $orgId): void
    {
        $slugs = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->pluck('slug')
            ->merge(['owner', 'admin', 'agent', 'member'])
            ->unique();

        foreach ($slugs as $role) {
            Cache::forget(self::rolePermissionsKey($orgId, $role));
        }
    }

    public static function invalidateAll(int $orgId): void
    {
        self::invalidateDashboard($orgId);
        self::invalidateDashboardDiscussions($orgId);
        self::invalidateCategories($orgId);
        self::invalidatePriorities($orgId);
        self::invalidateMembers($orgId);
        self::invalidateOrgFunctions($orgId);
        self::invalidateForms($orgId);
        self::invalidateReports($orgId);
        self::invalidateRolePermissions($orgId);
    }
}
