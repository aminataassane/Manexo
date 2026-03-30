<?php

namespace App\Livewire\Tickets;

use App\Enums\Permission;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.manexo-app')]
#[Title('Groupes')]
class Groups extends Component
{
    public function mount(): void
    {
        $user = Auth::user();
        abort_if(! $user instanceof \App\Models\User, 403);

        $orgId = (int) session('current_organization_id');
        abort_if(! $orgId, 403);
    }

    public function render()
    {
        $user = Auth::user();
        if (! $user instanceof \App\Models\User) {
            $user = null;
        }
        $orgId = (int) session('current_organization_id');

        $isStaff = $user ? $user->hasPermission(Permission::TicketsViewAll) : false;

        $canSeeSettings = $user && $user->hasAnyPermission([
            Permission::SettingsManageBranding,
            Permission::SettingsManageCategories,
            Permission::SettingsManagePriorities,
            Permission::SettingsManageFunctions,
            Permission::SettingsManageRoles,
            Permission::SettingsDeleteOrg,
        ]);

        $groups = collect();
        $ungroupedStats = null;
        $topTicketsByGroup = [];
        $agentsByGroup = [];
        $alerts = [];

        if ($user && $orgId) {
            $cacheKey = $isStaff ? CacheHelper::groupsOverviewKey($orgId) : null;

            $fetchData = function () use ($orgId, $isStaff, $user) {
                $memberFilter = '';
                $memberBindings = [];

                if (! $isStaff) {
                    $memberFilter = ' AND (t.created_by = ? OR EXISTS (SELECT 1 FROM ticket_assignees ta WHERE ta.ticket_id = t.id AND ta.user_id = ?) OR EXISTS (SELECT 1 FROM ticket_participants tp WHERE tp.ticket_id = t.id AND tp.user_id = ?))';
                    $memberBindings = [$user->id, $user->id, $user->id];
                }

                $groups = collect(DB::select("
                    SELECT tg.id, tg.name, tg.color, tg.icon, tg.slug,
                        COUNT(t.id) FILTER (WHERE t.status = 'open' AND t.archived_at IS NULL{$memberFilter}) AS open_count,
                        COUNT(t.id) FILTER (WHERE t.status = 'in_progress' AND t.archived_at IS NULL{$memberFilter}) AS in_progress_count,
                        COUNT(t.id) FILTER (WHERE t.status = 'pending' AND t.archived_at IS NULL{$memberFilter}) AS pending_count,
                        COUNT(t.id) FILTER (WHERE t.status IN ('resolved','closed') AND t.archived_at IS NULL{$memberFilter}) AS closed_count,
                        COUNT(t.id) FILTER (WHERE t.assigned_to IS NULL AND t.status NOT IN ('resolved','closed') AND t.archived_at IS NULL{$memberFilter}) AS unassigned_count,
                        COUNT(t.id) FILTER (WHERE t.sla_resolution_breached = true AND t.status NOT IN ('resolved','closed') AND t.archived_at IS NULL{$memberFilter}) AS sla_breached_count
                    FROM ticket_groups tg
                    LEFT JOIN tickets t ON t.ticket_group_id = tg.id AND t.deleted_at IS NULL
                    WHERE tg.organization_id = ? AND tg.is_active = true
                    GROUP BY tg.id
                    ORDER BY tg.sort_order, tg.name
                ", array_merge(
                    $memberBindings, $memberBindings, $memberBindings, $memberBindings, $memberBindings, $memberBindings,
                    [$orgId]
                )));

                $ungroupedBindings = [$orgId];
                $ungroupedWhere = '';
                if (! $isStaff) {
                    $ungroupedWhere = ' AND (created_by = ? OR EXISTS (SELECT 1 FROM ticket_assignees ta WHERE ta.ticket_id = tickets.id AND ta.user_id = ?) OR EXISTS (SELECT 1 FROM ticket_participants tp WHERE tp.ticket_id = tickets.id AND tp.user_id = ?))';
                    $ungroupedBindings = [$orgId, $user->id, $user->id, $user->id];
                }

                $ungroupedStats = DB::selectOne("
                    SELECT
                        COUNT(*) FILTER (WHERE status = 'open') AS open_count,
                        COUNT(*) FILTER (WHERE status = 'in_progress') AS in_progress_count,
                        COUNT(*) FILTER (WHERE status = 'pending') AS pending_count,
                        COUNT(*) FILTER (WHERE status IN ('resolved','closed')) AS closed_count,
                        COUNT(*) FILTER (WHERE assigned_to IS NULL AND status NOT IN ('resolved','closed')) AS unassigned_count,
                        COUNT(*) FILTER (WHERE sla_resolution_breached = true AND status NOT IN ('resolved','closed')) AS sla_breached_count
                    FROM tickets
                    WHERE organization_id = ? AND ticket_group_id IS NULL AND archived_at IS NULL AND deleted_at IS NULL{$ungroupedWhere}
                ", $ungroupedBindings);

                return ['groups' => $groups, 'ungroupedStats' => $ungroupedStats];
            };

            $data = $cacheKey
                ? Cache::remember($cacheKey, CacheHelper::TTL, $fetchData)
                : $fetchData();

            $groups = $data['groups'];
            $ungroupedStats = $data['ungroupedStats'];

            // Fetch top 5 tickets per group (actionable: open/in_progress, sorted by priority then age)
            $groupIds = $groups->pluck('id')->toArray();
            if (! empty($groupIds)) {
                $topTicketsByGroup = $this->fetchTopTickets($orgId, $groupIds, $isStaff, $user);
                $agentsByGroup = $this->fetchAgentsByGroup($orgId, $groupIds);
            }

            // Build alerts summary
            $alerts = $this->buildAlerts($groups, $ungroupedStats);
        }

        return view('livewire.tickets.groups', [
            'groups' => $groups,
            'ungroupedStats' => $ungroupedStats,
            'canSeeSettings' => $canSeeSettings,
            'topTicketsByGroup' => $topTicketsByGroup,
            'agentsByGroup' => $agentsByGroup,
            'alerts' => $alerts,
        ]);
    }

    /**
     * Fetch the top 5 most urgent/actionable tickets per group.
     */
    private function fetchTopTickets(int $orgId, array $groupIds, bool $isStaff, ?\App\Models\User $user): array
    {
        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
        $memberFilter = '';
        $memberBindings = [];

        if (! $isStaff && $user) {
            $memberFilter = ' AND (t.created_by = ? OR EXISTS (SELECT 1 FROM ticket_assignees ta WHERE ta.ticket_id = t.id AND ta.user_id = ?) OR EXISTS (SELECT 1 FROM ticket_participants tp WHERE tp.ticket_id = t.id AND tp.user_id = ?))';
            $memberBindings = [$user->id, $user->id, $user->id];
        }

        // Use window function to rank tickets within each group
        $rows = DB::select("
            SELECT * FROM (
                SELECT
                    t.id, t.public_id, t.subject, t.status, t.assigned_to, t.created_at,
                    t.ticket_group_id,
                    t.sla_resolution_breached, t.sla_resolution_deadline,
                    tp.name AS priority_name, tp.level AS priority_level,
                    u.name AS assignee_name,
                    ROW_NUMBER() OVER (
                        PARTITION BY t.ticket_group_id
                        ORDER BY
                            CASE WHEN t.sla_resolution_breached = true THEN 0 ELSE 1 END,
                            tp.level DESC NULLS LAST,
                            CASE WHEN t.assigned_to IS NULL THEN 0 ELSE 1 END,
                            t.created_at ASC
                    ) AS rn
                FROM tickets t
                LEFT JOIN ticket_priorities tp ON tp.id = t.ticket_priority_id
                LEFT JOIN users u ON u.id = t.assigned_to
                WHERE t.organization_id = ?
                    AND t.ticket_group_id IN ({$placeholders})
                    AND t.status NOT IN ('resolved', 'closed')
                    AND t.archived_at IS NULL
                    AND t.deleted_at IS NULL
                    {$memberFilter}
            ) ranked
            WHERE rn <= 5
            ORDER BY ticket_group_id, rn
        ", array_merge([$orgId], $groupIds, $memberBindings));

        $result = [];
        foreach ($rows as $row) {
            $gid = (int) $row->ticket_group_id;
            $result[$gid][] = $row;
        }

        return $result;
    }

    /**
     * Fetch agents assigned to active tickets in each group with their ticket count.
     */
    private function fetchAgentsByGroup(int $orgId, array $groupIds): array
    {
        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));

        $rows = DB::select("
            SELECT
                t.ticket_group_id,
                u.id AS user_id,
                u.name AS user_name,
                COUNT(t.id) AS ticket_count
            FROM tickets t
            INNER JOIN users u ON u.id = t.assigned_to
            WHERE t.organization_id = ?
                AND t.ticket_group_id IN ({$placeholders})
                AND t.status NOT IN ('resolved', 'closed')
                AND t.archived_at IS NULL
                AND t.deleted_at IS NULL
            GROUP BY t.ticket_group_id, u.id, u.name
            ORDER BY ticket_count DESC, u.name
        ", array_merge([$orgId], $groupIds));

        $result = [];
        foreach ($rows as $row) {
            $gid = (int) $row->ticket_group_id;
            $result[$gid][] = $row;
        }

        return $result;
    }

    /**
     * Build a summary of alerts across all groups.
     */
    private function buildAlerts(object $groups, ?object $ungroupedStats): array
    {
        $totalUnassigned = $groups->sum(fn ($g) => (int) ($g->unassigned_count ?? 0)) + (int) ($ungroupedStats->unassigned_count ?? 0);
        $totalSlaBreach = $groups->sum(fn ($g) => (int) ($g->sla_breached_count ?? 0)) + (int) ($ungroupedStats->sla_breached_count ?? 0);

        $alerts = [];
        if ($totalSlaBreach > 0) {
            $alerts[] = ['type' => 'danger', 'icon' => 'solar:danger-triangle-bold', 'text' => $totalSlaBreach.' '.__('ticket(s) en dépassement SLA')];
        }
        if ($totalUnassigned > 0) {
            $alerts[] = ['type' => 'warning', 'icon' => 'solar:user-cross-bold', 'text' => $totalUnassigned.' '.__('ticket(s) non assigné(s)')];
        }

        return $alerts;
    }
}
