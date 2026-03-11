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

        if ($user && $orgId) {
            // Staff sees all tickets → cacheable; non-staff is user-specific → no cache
            $cacheKey = $isStaff ? CacheHelper::groupsOverviewKey($orgId) : null;

            $fetchData = function () use ($orgId, $isStaff, $user) {
                $memberFilter = '';
                $memberBindings = [];

                if (! $isStaff) {
                    $memberFilter = " AND (t.created_by = ? OR EXISTS (SELECT 1 FROM ticket_assignees ta WHERE ta.ticket_id = t.id AND ta.user_id = ?) OR EXISTS (SELECT 1 FROM ticket_participants tp WHERE tp.ticket_id = t.id AND tp.user_id = ?))";
                    $memberBindings = [$user->id, $user->id, $user->id];
                }

                $groups = collect(DB::select("
                    SELECT tg.id, tg.name, tg.color, tg.icon, tg.slug,
                        COUNT(t.id) FILTER (WHERE t.status = 'open' AND t.archived_at IS NULL{$memberFilter}) AS open_count,
                        COUNT(t.id) FILTER (WHERE t.status = 'in_progress' AND t.archived_at IS NULL{$memberFilter}) AS in_progress_count,
                        COUNT(t.id) FILTER (WHERE t.status = 'pending' AND t.archived_at IS NULL{$memberFilter}) AS pending_count,
                        COUNT(t.id) FILTER (WHERE t.status IN ('resolved','closed') AND t.archived_at IS NULL{$memberFilter}) AS closed_count
                    FROM ticket_groups tg
                    LEFT JOIN tickets t ON t.ticket_group_id = tg.id
                    WHERE tg.organization_id = ? AND tg.is_active = true
                    GROUP BY tg.id
                    ORDER BY tg.sort_order, tg.name
                ", array_merge(
                    $memberBindings, $memberBindings, $memberBindings, $memberBindings,
                    [$orgId]
                )));

                $ungroupedBindings = [$orgId];
                $ungroupedWhere = '';
                if (! $isStaff) {
                    $ungroupedWhere = " AND (created_by = ? OR EXISTS (SELECT 1 FROM ticket_assignees ta WHERE ta.ticket_id = tickets.id AND ta.user_id = ?) OR EXISTS (SELECT 1 FROM ticket_participants tp WHERE tp.ticket_id = tickets.id AND tp.user_id = ?))";
                    $ungroupedBindings = [$orgId, $user->id, $user->id, $user->id];
                }

                $ungroupedStats = DB::selectOne("
                    SELECT
                        COUNT(*) FILTER (WHERE status = 'open') AS open_count,
                        COUNT(*) FILTER (WHERE status = 'in_progress') AS in_progress_count,
                        COUNT(*) FILTER (WHERE status = 'pending') AS pending_count,
                        COUNT(*) FILTER (WHERE status IN ('resolved','closed')) AS closed_count
                    FROM tickets
                    WHERE organization_id = ? AND ticket_group_id IS NULL AND archived_at IS NULL{$ungroupedWhere}
                ", $ungroupedBindings);

                return ['groups' => $groups, 'ungroupedStats' => $ungroupedStats];
            };

            $data = $cacheKey
                ? Cache::remember($cacheKey, CacheHelper::TTL, $fetchData)
                : $fetchData();

            $groups = $data['groups'];
            $ungroupedStats = $data['ungroupedStats'];
        }

        // Compute global totals across all groups + ungrouped
        $totalOpen = $groups->sum(fn ($g) => (int) $g->open_count) + (int) ($ungroupedStats->open_count ?? 0);
        $totalInProgress = $groups->sum(fn ($g) => (int) $g->in_progress_count) + (int) ($ungroupedStats->in_progress_count ?? 0);
        $totalPending = $groups->sum(fn ($g) => (int) $g->pending_count) + (int) ($ungroupedStats->pending_count ?? 0);
        $totalClosed = $groups->sum(fn ($g) => (int) $g->closed_count) + (int) ($ungroupedStats->closed_count ?? 0);

        return view('livewire.tickets.groups', [
            'groups' => $groups,
            'ungroupedStats' => $ungroupedStats,
            'canSeeSettings' => $canSeeSettings,
            'globalStats' => [
                'open' => $totalOpen,
                'in_progress' => $totalInProgress,
                'pending' => $totalPending,
                'closed' => $totalClosed,
                'total_groups' => $groups->count(),
            ],
        ]);
    }
}
