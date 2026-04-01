<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Helpers\CacheHelper;
use App\Models\Organization;
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
    /** 1 = shell instantané, 2 = KPI + graphiques (progressif). */
    public int $loadStage = 1;

    public function loadReportBody(): void
    {
        if ($this->loadStage < 2) {
            $this->loadStage = 2;
        }
    }

    /** @var string 'default' (30 days) | 'monthly' (current month) | 'yearly' (12 months) */
    public string $period = 'default';

    public function setPeriod(string $period): void
    {
        if (in_array($period, ['default', 'monthly', 'yearly'], true)) {
            if ($period === $this->period) {
                return;
            }

            $this->period = $period;
        }
    }

    public function updatedPeriod(string $value): void
    {
        if (! in_array($value, ['default', 'monthly', 'yearly'], true)) {
            $this->period = 'default';
        }
    }

    private function orgId(): int
    {
        return (int) session('current_organization_id');
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
     * Données vides pour le premier rendu (évite le cache lourd avant wire:init).
     *
     * @return array<string, mixed>
     */
    private function emptyReportsIndexData(): array
    {
        return [
            'kpis' => [
                'total' => 0,
                'open' => 0,
                'in_progress' => 0,
                'pending' => 0,
                'done' => 0,
                'created_7d' => 0,
                'done_7d' => 0,
                'avg_open_age_hours' => 0,
            ],
            'byStatus' => [],
            'createdLast7d' => [],
            'createdLast30d' => [],
            'topCategories' => [],
            'topAssignees' => [],
            'team' => ['owners' => 0, 'admins' => 0, 'agents' => 0, 'members' => 0],
            'trendTotal' => ['dir' => 'up', 'val' => 0],
            'trendNew30d' => ['dir' => 'up', 'val' => 0],
            'trendNewInPeriod' => ['dir' => 'up', 'val' => 0],
            'resolutionRate' => 0.0,
            'teamCapacityPercent' => 0,
            'performanceSeries' => [],
            'createdInPeriod' => 0,
            'slaKpis' => null,
        ];
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

        // Only users with reports.view permission
        if (! ($user instanceof \App\Models\User) || ! $user->hasPermission(Permission::ReportsView)) {
            abort(403);
        }

        $period = $this->period;

        if ($this->loadStage < 2) {
            return view('livewire.reports.index', $this->emptyReportsIndexData());
        }

        $cacheKey = CacheHelper::reportsKey($orgId, $period);
        $data = Cache::get($cacheKey);
        if ($data === null) {
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
                ->selectRaw('count(*) filter (where created_at >= ?) as created_7d', [$since7d])
                ->selectRaw("count(*) filter (where status in ('resolved','closed') and updated_at >= ?) as done_7d", [$since7d])
                ->selectRaw('count(*) filter (where created_at >= ? and created_at < ?) as created_prev_30d', [$since60d, $since30d])
                ->selectRaw("avg(extract(epoch from (? - created_at))) filter (where status in ('open','in_progress','pending')) as avg_open_age_seconds", [$now])
                ->selectRaw('count(*) filter (where created_at < ?) as total_30d_ago', [$since30d])
                ->selectRaw('count(*) filter (where created_at >= ?) as created_curr_30d', [$since30d])
                ->first();

            $total30dAgo = (int) ($kpiRow?->total_30d_ago ?? 0);
            $totalNow = (int) ($kpiRow?->total ?? 0);
            $createdCurr30d = (int) ($kpiRow?->created_curr_30d ?? 0);
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

            // Kept for backwards compatibility in the view payload, but not queried
            // because this page currently doesn't render these series directly.
            $byStatus = [];
            $createdLast7d = [];
            $createdLast30d = [];

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

            $team = [
                'owners' => 0,
                'admins' => 0,
                'agents' => 0,
                'members' => 0,
            ];

            // SLA KPIs (only if SLA is enabled for this organization)
            $slaKpis = null;
            $orgSettings = Organization::query()->where('id', $orgId)->value('settings');
            $orgSettings = is_array($orgSettings) ? $orgSettings : (is_string($orgSettings) ? json_decode($orgSettings, true) : []);
            $slaEnabled = (bool) ($orgSettings['sla']['enabled'] ?? false);
            if ($slaEnabled) {
                $slaBase = (clone $base)->whereNotNull('sla_policy_id');

                $slaRow = (clone $slaBase)
                    ->selectRaw('count(*) as total')
                    ->selectRaw('count(*) filter (where sla_first_response_met_at is not null and sla_first_response_breached = false) as fr_met')
                    ->selectRaw('count(*) filter (where sla_resolution_met_at is not null and sla_resolution_breached = false) as res_met')
                    ->selectRaw('count(*) filter (where sla_first_response_breached = true) as fr_breached')
                    ->selectRaw('count(*) filter (where sla_resolution_breached = true) as res_breached')
                    ->first();

                $slaTotal = (int) ($slaRow->total ?? 0);
                if ($slaTotal > 0) {
                    $slaPriorityStats = (clone $slaBase)
                        ->join('ticket_priorities as tp', 'tickets.ticket_priority_id', '=', 'tp.id')
                        ->selectRaw('tp.name as priority_name, tp.level as priority_level')
                        ->selectRaw('count(*) as total')
                        ->selectRaw('count(*) filter (where tickets.sla_first_response_met_at is not null and tickets.sla_first_response_breached = false) as fr_met')
                        ->selectRaw('count(*) filter (where tickets.sla_resolution_met_at is not null and tickets.sla_resolution_breached = false) as res_met')
                        ->groupBy('tp.name', 'tp.level')
                        ->orderByDesc('tp.level')
                        ->get()
                        ->map(fn ($r) => [
                            'name' => (string) $r->priority_name,
                            'total' => (int) $r->total,
                            'fr_met_pct' => (int) $r->total > 0 ? round(((int) $r->fr_met / (int) $r->total) * 100, 1) : 0,
                            'res_met_pct' => (int) $r->total > 0 ? round(((int) $r->res_met / (int) $r->total) * 100, 1) : 0,
                        ])
                        ->all();

                    $slaKpis = [
                        'total' => (int) $slaRow->total,
                        'fr_met_pct' => round(((int) $slaRow->fr_met / (int) $slaRow->total) * 100, 1),
                        'res_met_pct' => round(((int) $slaRow->res_met / (int) $slaRow->total) * 100, 1),
                        'fr_breached_pct' => round(((int) $slaRow->fr_breached / (int) $slaRow->total) * 100, 1),
                        'res_breached_pct' => round(((int) $slaRow->res_breached / (int) $slaRow->total) * 100, 1),
                        'by_priority' => $slaPriorityStats,
                    ];
                }
            }

            $data = compact(
                'kpis', 'byStatus', 'createdLast7d', 'createdLast30d',
                'topCategories', 'topAssignees', 'team', 'trendTotal',
                'trendNew30d', 'trendNewInPeriod', 'resolutionRate',
                'teamCapacityPercent', 'performanceSeries', 'createdInPeriod',
                'slaKpis',
            );
            Cache::put($cacheKey, $data, CacheHelper::TTL);
        }

        return view('livewire.reports.index', $data);
    }
}
