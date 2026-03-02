<?php

namespace App\Enums;

enum Permission: string
{
    // ─── Tickets ─────────────────────────────────────────────────────
    case TicketsViewAll = 'tickets.view_all';
    case TicketsCreate = 'tickets.create';
    case TicketsEdit = 'tickets.edit';
    case TicketsDelete = 'tickets.delete';
    case TicketsChangeStatus = 'tickets.change_status';
    case TicketsAssign = 'tickets.assign';
    case TicketsArchive = 'tickets.archive';
    case TicketsViewTrash = 'tickets.view_trash';

    // ─── Equipe ──────────────────────────────────────────────────────
    case TeamInvite = 'team.invite';
    case TeamEditRole = 'team.edit_role';
    case TeamRemove = 'team.remove';

    // ─── Formulaires ────────────────────────────────────────────
    case FormsManage = 'forms.manage';
    case FormsAssign = 'forms.assign';
    case FormsViewResponses = 'forms.view_responses';

    // ─── Parametres ──────────────────────────────────────────────────
    case SettingsManageBranding = 'settings.manage_branding';
    case SettingsManageCategories = 'settings.manage_categories';
    case SettingsManagePriorities = 'settings.manage_priorities';
    case SettingsManageFunctions = 'settings.manage_functions';
    case SettingsManageForms = 'settings.manage_forms';
    case SettingsManageRoles = 'settings.manage_roles';
    case SettingsDeleteOrg = 'settings.delete_org';

    // ─── Rapports ────────────────────────────────────────────────────
    case ReportsView = 'reports.view';
    case ReportsViewTasks = 'reports.view_tasks';
    case ReportsShareTasks = 'reports.share_tasks';

    // ─── Discussions ─────────────────────────────────────────────────
    case DiscussionsViewInternalNotes = 'discussions.view_internal_notes';
    case DiscussionsWriteInternalNotes = 'discussions.write_internal_notes';

    /**
     * Permissions grouped by category (for UI display).
     *
     * @return array<string, Permission[]>
     */
    public static function grouped(): array
    {
        return [
            'tickets' => [
                self::TicketsViewAll,
                self::TicketsCreate,
                self::TicketsEdit,
                self::TicketsDelete,
                self::TicketsChangeStatus,
                self::TicketsAssign,
                self::TicketsArchive,
                self::TicketsViewTrash,
            ],
            'team' => [
                self::TeamInvite,
                self::TeamEditRole,
                self::TeamRemove,
            ],
            'forms' => [
                self::FormsManage,
                self::FormsAssign,
                self::FormsViewResponses,
            ],
            'settings' => [
                self::SettingsManageBranding,
                self::SettingsManageCategories,
                self::SettingsManagePriorities,
                self::SettingsManageFunctions,
                self::SettingsManageForms,
                self::SettingsManageRoles,
                self::SettingsDeleteOrg,
            ],
            'reports' => [
                self::ReportsView,
                self::ReportsViewTasks,
                self::ReportsShareTasks,
            ],
            'discussions' => [
                self::DiscussionsViewInternalNotes,
                self::DiscussionsWriteInternalNotes,
            ],
        ];
    }

    /**
     * Default permissions for a given role.
     *
     * @return Permission[]
     */
    public static function defaultsForRole(OrganizationRole $role): array
    {
        return match ($role) {
            OrganizationRole::Owner => self::cases(),

            OrganizationRole::Admin => array_filter(
                self::cases(),
                fn (Permission $p) => $p !== self::SettingsDeleteOrg,
            ),

            OrganizationRole::Agent => [
                self::TicketsViewAll,
                self::TicketsCreate,
                self::TicketsEdit,
                self::TicketsDelete,
                self::TicketsChangeStatus,
                self::TicketsAssign,
                self::TicketsArchive,
                self::TicketsViewTrash,
                self::FormsViewResponses,
                self::ReportsView,
                self::ReportsViewTasks,
                self::ReportsShareTasks,
                self::DiscussionsViewInternalNotes,
                self::DiscussionsWriteInternalNotes,
            ],

            OrganizationRole::Member => [
                self::TicketsCreate,
                self::ReportsViewTasks,
            ],
        };
    }
}
