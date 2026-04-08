<?php

namespace App\Helpers;

use App\Models\OrganizationMembership;
use App\Models\RoleDefinition;
use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /** Cache TTL in seconds (30 minutes) — general data. */
    public const TTL = 1800;

    /** Short TTL for notification counts (1 minute). */
    public const TTL_SHORT = 60;

    /** Long TTL for config-like data that rarely changes (1 hour). */
    public const TTL_CONFIG = 3600;

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

    /** Page `/profile` : organisations, sessions et invitations en un seul aller-retour cache. */
    public static function profileIndexBootstrapKey(int $userId): string
    {
        return "profile:index_bootstrap:v1:{$userId}";
    }

    public static function forgetProfileIndexBootstrap(int $userId): void
    {
        Cache::forget(self::profileIndexBootstrapKey($userId));
        Cache::forget("profile:orgs:{$userId}");
        Cache::forget("profile:sessions:{$userId}");
        Cache::forget("profile:pending_invitations:{$userId}");
    }

    public static function orgFunctionsKey(int $orgId): string
    {
        return "org_functions:{$orgId}";
    }

    public static function orgMemberRolesKey(int $orgId): string
    {
        return "org_member_roles:{$orgId}";
    }

    /** Assignable users list for ticket sidebar (versioned for cache bust on query changes). */
    public static function staffUsersKey(int $orgId): string
    {
        return "staff_users:v2:{$orgId}";
    }

    /** Assignable users for ticket index filters (versioned with staff_users). */
    public static function ticketAssignableUsersKey(int $orgId): string
    {
        return "assignable_users:v2:{$orgId}";
    }

    public static function ticketGroupsKey(int $orgId, bool $activeOnly): string
    {
        $variant = $activeOnly ? 'active' : 'all';

        return "ticket_groups:{$orgId}:{$variant}";
    }

    /**
     * Page « Créer un ticket » : une seule entrée cache pour catégories, priorités, groupes et (optionnel) staff assignable.
     */
    public static function createTicketBootstrapKey(int $orgId, bool $withAssignableStaff): string
    {
        return 'create_ticket_bootstrap:v3:'.$orgId.':'.($withAssignableStaff ? '1' : '0');
    }

    public static function invalidateCreateTicketBootstrap(int $orgId): void
    {
        Cache::forget(self::createTicketBootstrapKey($orgId, true));
        Cache::forget(self::createTicketBootstrapKey($orgId, false));
    }

    public static function groupsOverviewKey(int $orgId): string
    {
        return "groups_overview:{$orgId}";
    }

    public static function kbCategoriesKey(int $orgId): string
    {
        return "kb_categories:{$orgId}";
    }

    public static function kbArticlesKey(int $orgId): string
    {
        return "kb_articles:{$orgId}";
    }

    /** Categories + article list for KB index default view (single cache round-trip). */
    public static function kbIndexDefaultKey(int $orgId): string
    {
        return "kb_index_default:{$orgId}";
    }

    public static function slaPoliciesKey(int $orgId): string
    {
        return "sla_policies:{$orgId}";
    }

    public static function automationRulesKey(int $orgId, string $trigger): string
    {
        return "automation_rules:{$orgId}:{$trigger}";
    }

    /**
     * Ticket counts key (stats + view counts) — versioned so we can invalidate
     * all per-user / per-group variants at once by bumping the version counter.
     */
    public static function ticketCountsKey(int $orgId, int $userId, string $group): string
    {
        $ver = (int) Cache::get("tickets:counts_ver:{$orgId}", 0);

        return "tickets:counts:{$orgId}:{$userId}:{$ver}:".($group !== '' ? $group : 'all');
    }

    /**
     * Kanban board columns — uses the same tickets:counts_ver as stats so both stay in sync
     * after invalidateTicketCounts (status change, move, create, etc.).
     */
    public static function kanbanTicketsKey(int $orgId, int $userId, string $filterHash): string
    {
        $ver = (int) Cache::get("tickets:counts_ver:{$orgId}", 0);

        return "tickets:kanban:{$orgId}:{$userId}:{$filterHash}:v{$ver}";
    }

    /** Liste « formulaires d'équipe » (publiés, sans cible user) — Mes formulaires. */
    public static function formsListKey(int $orgId): string
    {
        return "forms_list:{$orgId}";
    }

    /** Liste complète des formulaires (admin éditeur + relations) — ne pas partager la clé user. */
    public static function formsListAdminKey(int $orgId): string
    {
        return "forms_list_admin:{$orgId}";
    }

    /** Mes formulaires : liste des assignations (par onglet). */
    public static function userFormsAssignmentsKey(int $orgId, int $userId, string $tab): string
    {
        return "user_forms:assignments:{$orgId}:{$userId}:{$tab}";
    }

    /** Mes formulaires : cartes statistiques (pending, overdue, etc.). */
    public static function userFormsStatsKey(int $orgId, int $userId): string
    {
        return "user_forms:stats:{$orgId}:{$userId}";
    }

    /** Paramètres : liste des formulaires publiés (ids + noms) pour les selects. */
    public static function settingsPublishedFormsKey(int $orgId): string
    {
        return "settings_published_forms:{$orgId}";
    }

    /** Paramètres : toutes les règles d'automatisation (écran admin). */
    public static function settingsAutomationRulesListKey(int $orgId): string
    {
        return "settings_automation_rules_list:{$orgId}";
    }

    /** Paramètres : tokens API Sanctum pour l'onglet API. */
    public static function settingsApiTokensKey(int $orgId): string
    {
        return "settings_api_tokens:{$orgId}";
    }

    /** Paramètres : endpoints webhooks. */
    public static function settingsWebhookEndpointsKey(int $orgId): string
    {
        return "settings_webhook_endpoints:{$orgId}";
    }

    /** Paramètres : catégories KB (admin, toutes). */
    public static function settingsKbCategoriesAdminKey(int $orgId): string
    {
        return "settings_kb_categories_admin:{$orgId}";
    }

    /** Paramètres : articles KB (admin, avec catégorie). */
    public static function settingsKbArticlesAdminKey(int $orgId): string
    {
        return "settings_kb_articles_admin:{$orgId}";
    }

    /** Paramètres : définitions de rôles (liste onglet rôles). */
    public static function settingsRolesListKey(int $orgId): string
    {
        return "settings_roles:{$orgId}";
    }

    /** Paramètres : comptages membres par rôle. */
    public static function settingsRoleCountsKey(int $orgId): string
    {
        return "settings_role_counts:{$orgId}";
    }

    /** Paramètres : liste membres (onglet rôles / maintenance). */
    public static function settingsMembersListKey(int $orgId): string
    {
        return "settings_members:{$orgId}";
    }

    /** Paramètres : stats maintenance (cartes). */
    public static function settingsMaintenanceStatsKey(int $orgId): string
    {
        return "settings_maint_stats:{$orgId}";
    }

    public static function invalidateSettingsMaintenanceStats(int $orgId): void
    {
        Cache::forget(self::settingsMaintenanceStatsKey($orgId));
    }

    public static function reportsKey(int $orgId, string $period): string
    {
        return "reports:{$orgId}:{$period}";
    }

    public static function dailyReportKey(int $orgId, string $date): string
    {
        return "daily_report:{$orgId}:{$date}";
    }

    /**
     * Bump when any notification affecting this user changes (any org).
     * Invalidates per-org cached counts without enumerating organization ids.
     */
    public static function bumpUserNotificationCachesVersion(int $userId): void
    {
        Cache::increment("user_notification_caches_ver:{$userId}");
    }

    /** Notifications: unread count (topbar bell), scoped to current org. */
    public static function notificationsUnreadCountKey(int $userId, int $orgId): string
    {
        $ver = (int) Cache::get("user_notification_caches_ver:{$userId}", 0);

        return "notifications:unread_count:{$userId}:org:{$orgId}:v{$ver}";
    }

    /** Sidebar: discussions unread badge (DiscussionNewMessage + DiscussionInvite only), per org. */
    public static function sidebarDiscussionsUnreadKey(int $userId, int $orgId): string
    {
        $ver = (int) Cache::get("user_notification_caches_ver:{$userId}", 0);

        return "sidebar:discussions_unread:{$userId}:org:{$orgId}:v{$ver}";
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
        Cache::forget(self::groupsOverviewKey($orgId));
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
        self::invalidateCreateTicketBootstrap($orgId);
        self::invalidateSettingsMaintenanceStats($orgId);
    }

    public static function invalidatePriorities(int $orgId): void
    {
        Cache::forget(self::prioritiesKey($orgId, true));
        Cache::forget(self::prioritiesKey($orgId, false));
        self::invalidateCreateTicketBootstrap($orgId);
        self::invalidateSettingsMaintenanceStats($orgId);
    }

    public static function invalidateMembers(int $orgId): void
    {
        Cache::forget(self::membersKey($orgId));
        Cache::forget(self::orgMemberRolesKey($orgId));
        Cache::forget("staff_users:{$orgId}");
        Cache::forget(self::staffUsersKey($orgId));
        Cache::forget("assignable_users:{$orgId}");
        Cache::forget(self::ticketAssignableUsersKey($orgId));
        self::invalidateCreateTicketBootstrap($orgId);
    }

    public static function invalidateOrgFunctions(int $orgId): void
    {
        Cache::forget(self::orgFunctionsKey($orgId));
    }

    /**
     * Bump the version counter so all per-user ticket-count caches become stale.
     * Old entries expire naturally via TTL — no need to enumerate user keys.
     */
    public static function invalidateTicketCounts(int $orgId): void
    {
        Cache::increment("tickets:counts_ver:{$orgId}");
        Cache::forget(self::groupsOverviewKey($orgId));
        self::invalidateSettingsMaintenanceStats($orgId);
    }

    public static function invalidateTicketGroups(int $orgId): void
    {
        Cache::forget(self::ticketGroupsKey($orgId, true));
        Cache::forget(self::ticketGroupsKey($orgId, false));
        Cache::forget(self::groupsOverviewKey($orgId));
        self::invalidateCreateTicketBootstrap($orgId);
    }

    public static function invalidateKnowledgeBase(int $orgId): void
    {
        Cache::forget(self::kbCategoriesKey($orgId));
        Cache::forget(self::kbArticlesKey($orgId));
        Cache::forget(self::kbIndexDefaultKey($orgId));
        Cache::forget(self::settingsKbCategoriesAdminKey($orgId));
        Cache::forget(self::settingsKbArticlesAdminKey($orgId));
    }

    public static function invalidateSlaPolicies(int $orgId): void
    {
        Cache::forget(self::slaPoliciesKey($orgId));
    }

    public static function invalidateAutomationRules(int $orgId): void
    {
        $triggers = ['ticket_created', 'status_changed', 'priority_changed', 'sla_at_risk', 'sla_breached'];
        foreach ($triggers as $trigger) {
            Cache::forget(self::automationRulesKey($orgId, $trigger));
        }
        Cache::forget(self::settingsAutomationRulesListKey($orgId));
    }

    public static function invalidateForms(int $orgId): void
    {
        Cache::forget(self::formsListKey($orgId));
        Cache::forget(self::formsListAdminKey($orgId));
        Cache::forget(self::settingsPublishedFormsKey($orgId));
        self::invalidateSettingsMaintenanceStats($orgId);
    }

    /**
     * Invalide le cache « Mes formulaires » + widget dashboard pour un utilisateur
     * (après soumission, assignation, etc.).
     */
    public static function invalidateUserFormsCache(int $orgId, int $userId): void
    {
        foreach (['pending', 'submitted', 'all'] as $tab) {
            Cache::forget(self::userFormsAssignmentsKey($orgId, $userId, $tab));
        }
        Cache::forget(self::userFormsStatsKey($orgId, $userId));
        self::invalidatePendingForms($orgId, $userId);
    }

    public static function invalidateSettingsApiTokens(int $orgId): void
    {
        Cache::forget(self::settingsApiTokensKey($orgId));
    }

    public static function invalidateSettingsWebhooks(int $orgId): void
    {
        Cache::forget(self::settingsWebhookEndpointsKey($orgId));
    }

    /** Invalider les caches de liste rôles / comptages (après CRUD rôle). */
    public static function invalidateSettingsRoles(int $orgId): void
    {
        Cache::forget(self::settingsRolesListKey($orgId));
        Cache::forget(self::settingsRoleCountsKey($orgId));
        Cache::forget(self::settingsMaintenanceStatsKey($orgId));
    }

    public static function invalidateReports(int $orgId): void
    {
        Cache::forget(self::reportsKey($orgId, 'default'));
        Cache::forget(self::reportsKey($orgId, 'monthly'));
        Cache::forget(self::reportsKey($orgId, 'yearly'));
        self::invalidateDailyReport($orgId);
    }

    public static function invalidateDailyReport(int $orgId): void
    {
        $today = now()->toDateString();
        Cache::forget(self::dailyReportKey($orgId, $today));
    }

    public static function invalidateNotificationsCount(int $userId): void
    {
        self::bumpUserNotificationCachesVersion($userId);
        Cache::forget('sidebar_notif_unread:'.$userId);
    }

    public static function invalidateSidebarDiscussionsUnread(int $userId): void
    {
        self::bumpUserNotificationCachesVersion($userId);
        Cache::forget('sidebar_disc_unread:'.$userId);
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

    /**
     * Invalide les caches d’hydratation de l’organisation utilisés par EnsureOrganizationIsSelected
     * (sinon primary_color / nom restent obsolètes jusqu’à expiration du TTL, ~5 min).
     */
    public static function invalidateOrganizationIdentityCache(int $orgId): void
    {
        Cache::forget("sa_org:{$orgId}");

        OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->pluck('user_id')
            ->unique()
            ->each(function ($userId) use ($orgId): void {
                Cache::forget('user_org:'.(int) $userId.':'.$orgId);
            });
    }

    public static function invalidateAll(int $orgId): void
    {
        self::invalidateDashboard($orgId);
        self::invalidateDashboardDiscussions($orgId);
        self::invalidateCategories($orgId);
        self::invalidatePriorities($orgId);
        self::invalidateMembers($orgId);
        self::invalidateOrgFunctions($orgId);
        self::invalidateTicketGroups($orgId);
        self::invalidateTicketCounts($orgId);
        self::invalidateSlaPolicies($orgId);
        self::invalidateAutomationRules($orgId);
        self::invalidateForms($orgId);
        self::invalidateReports($orgId);
        self::invalidateKnowledgeBase($orgId);
        self::invalidateRolePermissions($orgId);
        Cache::forget(self::settingsPublishedFormsKey($orgId));
        Cache::forget(self::settingsAutomationRulesListKey($orgId));
        Cache::forget(self::settingsApiTokensKey($orgId));
        Cache::forget(self::settingsWebhookEndpointsKey($orgId));
        Cache::forget(self::settingsRolesListKey($orgId));
        Cache::forget(self::settingsRoleCountsKey($orgId));
        Cache::forget(self::settingsMembersListKey($orgId));
        Cache::forget(self::settingsMaintenanceStatsKey($orgId));
        self::invalidateOrganizationIdentityCache($orgId);
    }
}
