<?php

namespace App\Livewire\Reports;

use App\Enums\OrganizationRole;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.manexo-app')]
#[Title('Rapports')]
class Index extends Component
{
    private function orgId(): int
    {
        return (int) session('current_organization_id');
    }

    private function currentRole(): string
    {
        $user = Auth::user();
        $orgId = $this->orgId();

        if (! $user instanceof \App\Models\User || ! $orgId) {
            return OrganizationRole::Member->value;
        }

        return (string) ($user->organizations()->whereKey($orgId)->first()?->pivot?->role ?? OrganizationRole::Member->value);
    }

    public function render()
    {
        $user = Auth::user();
        $orgId = $this->orgId();

        if (! $user || ! $orgId) {
            return redirect()->route('organizations.select');
        }

        $role = $this->currentRole();

        // Staff only (owner/admin/agent) for reports
        if (! in_array($role, [OrganizationRole::Owner->value, OrganizationRole::Admin->value, OrganizationRole::Agent->value], true)) {
            abort(403);
        }

        $now = Carbon::now();
        $since7d = $now->copy()->subDays(7);
        $since30d = $now->copy()->subDays(30);

        $base = Ticket::query()->where('tickets.organization_id', $orgId);

        $kpiRow = (clone $base)
            ->selectRaw('count(*) as total')
            ->selectRaw("count(*) filter (where status = 'open') as open_count")
            ->selectRaw("count(*) filter (where status = 'in_progress') as in_progress_count")
            ->selectRaw("count(*) filter (where status = 'pending') as pending_count")
            ->selectRaw("count(*) filter (where status in ('resolved','closed')) as done_count")
            ->selectRaw("count(*) filter (where created_at >= ?) as created_7d", [$since7d])
            ->selectRaw("count(*) filter (where status in ('resolved','closed') and updated_at >= ?) as done_7d", [$since7d])
            ->selectRaw("avg(extract(epoch from (? - created_at))) filter (where status in ('open','in_progress','pending')) as avg_open_age_seconds", [$now])
            ->first();

        $kpis = [
            'total' => (int) ($kpiRow?->total ?? 0),
            'open' => (int) ($kpiRow?->open_count ?? 0),
            'in_progress' => (int) ($kpiRow?->in_progress_count ?? 0),
            'pending' => (int) ($kpiRow?->pending_count ?? 0),
            'done' => (int) ($kpiRow?->done_count ?? 0),
            'created_7d' => (int) ($kpiRow?->created_7d ?? 0),
            'done_7d' => (int) ($kpiRow?->done_7d ?? 0),
            'avg_open_age_hours' => (int) round(((float) ($kpiRow?->avg_open_age_seconds ?? 0)) / 3600),
        ];

        $byStatus = (clone $base)
            ->select('status', DB::raw('count(*) as c'))
            ->groupBy('status')
            ->orderByDesc('c')
            ->get()
            ->map(function ($r) {
                $statusValue = is_object($r->status) ? $r->status->value : (string) $r->status;

                return ['status' => $statusValue, 'count' => (int) $r->c];
            })
            ->all();

        $createdLast7d = (clone $base)
            ->where('created_at', '>=', $since7d)
            ->selectRaw("date_trunc('day', created_at) as d, count(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->map(fn ($r) => ['date' => Carbon::parse($r->d)->toDateString(), 'count' => (int) $r->c])
            ->all();

        $createdLast30d = (clone $base)
            ->where('created_at', '>=', $since30d)
            ->selectRaw("date_trunc('day', created_at) as d, count(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->map(fn ($r) => ['date' => Carbon::parse($r->d)->toDateString(), 'count' => (int) $r->c])
            ->all();

        $topCategories = (clone $base)
            ->join('ticket_categories as tc', 'tickets.ticket_category_id', '=', 'tc.id')
            ->selectRaw('tc.name as name, count(*) as c')
            ->groupBy('tc.name')
            ->orderByDesc('c')
            ->limit(6)
            ->get()
            ->map(fn ($r) => ['name' => (string) $r->name, 'count' => (int) $r->c])
            ->all();

        $topAssignees = (clone $base)
            ->whereNotNull('assigned_to')
            ->join('users as u', 'tickets.assigned_to', '=', 'u.id')
            ->selectRaw('u.name as name, count(*) as c')
            ->groupBy('u.name')
            ->orderByDesc('c')
            ->limit(6)
            ->get()
            ->map(fn ($r) => ['name' => (string) $r->name, 'count' => (int) $r->c])
            ->all();

        $rolesCount = OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->selectRaw("count(*) filter (where role = 'owner') as owners")
            ->selectRaw("count(*) filter (where role = 'admin') as admins")
            ->selectRaw("count(*) filter (where role = 'agent') as agents")
            ->selectRaw("count(*) filter (where role = 'member') as members")
            ->first();

        $team = [
            'owners' => (int) ($rolesCount?->owners ?? 0),
            'admins' => (int) ($rolesCount?->admins ?? 0),
            'agents' => (int) ($rolesCount?->agents ?? 0),
            'members' => (int) ($rolesCount?->members ?? 0),
        ];

        return view('livewire.reports.index', [
            'role' => $role,
            'kpis' => $kpis,
            'byStatus' => $byStatus,
            'createdLast7d' => $createdLast7d,
            'createdLast30d' => $createdLast30d,
            'topCategories' => $topCategories,
            'topAssignees' => $topAssignees,
            'team' => $team,
        ]);
    }
}

