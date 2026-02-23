<?php

namespace App\Livewire\Reports;

use App\Enums\OrganizationRole;
use App\Helpers\CacheHelper;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.manexo-app')]
#[Title('Rapports')]
class Index extends Component
{
    /** @var string 'default' (30 days) | 'monthly' (current month) | 'yearly' (12 months) */
    public string $period = 'default';

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

    /** @return array{dir: string, val: int} */
    private function percentChange(int $previous, int $currentMinusPrevious): array
    {
        if ($previous === 0) {
            return ['dir' => $currentMinusPrevious >= 0 ? 'up' : 'down', 'val' => (int) min(99, abs($currentMinusPrevious))];
        }
        $pct = (int) round(($currentMinusPrevious / $previous) * 100);
        $pct = max(-99, min(99, $pct));

        return ['dir' => $pct >= 0 ? 'up' : 'down', 'val' => (int) abs($pct)];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Ticket>  $base
     * @return array<int, array{date: string, count: int}>
     */
    private function yearlySeriesWithMissingMonths($base, Carbon $periodSince, Carbon $now): array
    {
        $byMonth = (clone $base)
            ->where('created_at', '>=', $periodSince)
            ->selectRaw("date_trunc('month', created_at) as d, count(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy(fn ($r) => Carbon::parse($r->d)->format('Y-m'));

        $out = [];
        for ($m = $periodSince->copy(); $m->lte($now); $m->addMonth()) {
            $key = $m->format('Y-m');
            $out[] = ['date' => $key, 'count' => (int) ($byMonth->get($key)?->c ?? 0)];
        }

        return $out;
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

        $period = $this->period;
        $data = Cache::remember(CacheHelper::reportsKey($orgId, $period), CacheHelper::TTL, function () use ($orgId, $period) {
            $now = Carbon::now();
            $since7d = $now->copy()->subDays(7);
            $since30d = $now->copy()->subDays(30);
            $since60d = $now->copy()->subDays(60);

            $periodSince = match ($period) {
                'monthly' => $now->copy()->startOfMonth(),
                'yearly' => $now->copy()->subMonths(12)->startOfMonth(),
                default => $since30d,
            };

            $base = Ticket::query()->where('tickets.organization_id', $orgId);

            $kpiRow = (clone $base)
                ->selectRaw('count(*) as total')
                ->selectRaw("count(*) filter (where status = 'open') as open_count")
                ->selectRaw("count(*) filter (where status = 'in_progress') as in_progress_count")
                ->selectRaw("count(*) filter (where status = 'pending') as pending_count")
                ->selectRaw("count(*) filter (where status in ('resolved','closed')) as done_count")
                ->selectRaw("count(*) filter (where created_at >= ?) as created_7d", [$since7d])
                ->selectRaw("count(*) filter (where status in ('resolved','closed') and updated_at >= ?) as done_7d", [$since7d])
                ->selectRaw("count(*) filter (where created_at >= ? and created_at < ?) as created_prev_30d", [$since60d, $since30d])
                ->selectRaw("avg(extract(epoch from (? - created_at))) filter (where status in ('open','in_progress','pending')) as avg_open_age_seconds", [$now])
                ->first();

            $total30dAgo = (clone $base)->where('created_at', '<', $since30d)->count();
            $totalNow = (int) ($kpiRow?->total ?? 0);
            $createdCurr30d = (int) (clone $base)->where('created_at', '>=', $since30d)->count();
            $createdPrev30d = (int) ($kpiRow?->created_prev_30d ?? 0);

            $kpis = [
                'total' => $totalNow,
                'open' => (int) ($kpiRow?->open_count ?? 0),
                'in_progress' => (int) ($kpiRow?->in_progress_count ?? 0),
                'pending' => (int) ($kpiRow?->pending_count ?? 0),
                'done' => (int) ($kpiRow?->done_count ?? 0),
                'created_7d' => (int) ($kpiRow?->created_7d ?? 0),
                'done_7d' => (int) ($kpiRow?->done_7d ?? 0),
                'avg_open_age_hours' => (int) round(((float) ($kpiRow?->avg_open_age_seconds ?? 0)) / 3600),
            ];

            $trendTotal = $this->percentChange($total30dAgo, $totalNow - $total30dAgo);
            $trendNew30d = $this->percentChange($createdPrev30d, $createdCurr30d - $createdPrev30d);

            $resolutionRate = $kpis['total'] > 0
                ? round(($kpis['done'] / $kpis['total']) * 100, 1)
                : 0.0;

            $activeCount = $kpis['open'] + $kpis['in_progress'];
            $teamCapacityPercent = $kpis['total'] > 0
                ? min(100, (int) round(($activeCount / $kpis['total']) * 100))
                : 0;

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

            $since14d = $now->copy()->subDays(14);
            $performanceSeries = match ($period) {
                'yearly' => $this->yearlySeriesWithMissingMonths($base, $periodSince, $now),
                'monthly' => (clone $base)
                    ->where('created_at', '>=', $periodSince)
                    ->selectRaw("date_trunc('day', created_at) as d, count(*) as c")
                    ->groupBy('d')
                    ->orderBy('d')
                    ->get()
                    ->map(fn ($r) => ['date' => Carbon::parse($r->d)->toDateString(), 'count' => (int) $r->c])
                    ->all(),
                default => (clone $base)
                    ->where('created_at', '>=', $since14d)
                    ->selectRaw("date_trunc('day', created_at) as d, count(*) as c")
                    ->groupBy('d')
                    ->orderBy('d')
                    ->get()
                    ->map(fn ($r) => ['date' => Carbon::parse($r->d)->toDateString(), 'count' => (int) $r->c])
                    ->all(),
            };

            $createdInPeriod = (int) (clone $base)->where('created_at', '>=', $periodSince)->count();
            $createdInPeriodPrev = match ($period) {
                'monthly' => (clone $base)
                    ->whereBetween('created_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()])
                    ->count(),
                'yearly' => (clone $base)
                    ->where('created_at', '>=', $now->copy()->subMonths(24)->startOfMonth())
                    ->where('created_at', '<', $periodSince)
                    ->count(),
                default => $createdPrev30d,
            };
            $trendNewInPeriod = $this->percentChange($createdInPeriodPrev, $createdInPeriod - $createdInPeriodPrev);

            $topCategories = (clone $base)
                ->join('ticket_categories as tc', 'tickets.ticket_category_id', '=', 'tc.id')
                ->selectRaw('tc.name as name, count(*) as c')
                ->groupBy('tc.name')
                ->orderByDesc('c')
                ->limit(6)
                ->get()
                ->map(fn ($r) => ['name' => (string) $r->name, 'count' => (int) $r->c])
                ->all();

            $topAssigneesRaw = (clone $base)
                ->whereNotNull('assigned_to')
                ->join('users as u', 'tickets.assigned_to', '=', 'u.id')
                ->selectRaw('u.id as user_id, u.name as name, count(*) as total')
                ->selectRaw("count(*) filter (where tickets.status in ('resolved','closed')) as resolved")
                ->groupBy('u.id', 'u.name')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

            $topAssignees = $topAssigneesRaw->map(function ($r) {
                $total = (int) $r->total;
                $resolved = (int) $r->resolved;

                return [
                    'name' => (string) $r->name,
                    'count' => $total,
                    'resolved' => $resolved,
                    'efficiency' => $total > 0 ? (int) round(($resolved / $total) * 100) : 0,
                ];
            })->all();

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

            return compact(
                'kpis', 'byStatus', 'createdLast7d', 'createdLast30d',
                'topCategories', 'topAssignees', 'team', 'trendTotal',
                'trendNew30d', 'trendNewInPeriod', 'resolutionRate',
                'teamCapacityPercent', 'performanceSeries', 'createdInPeriod',
            );
        });

        return view('livewire.reports.index', array_merge(['role' => $role], $data));
    }
}

