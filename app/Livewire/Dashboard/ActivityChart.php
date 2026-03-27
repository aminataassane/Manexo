<?php

namespace App\Livewire\Dashboard;

use App\Helpers\CacheHelper;
use App\Livewire\Dashboard\Concerns\WithOrganization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ActivityChart extends Component
{
    use WithOrganization;

    /** 7 or 30 for chart range. */
    public int $chartDays = 7;

    public function setChartDays(int $days): void
    {
        if (in_array($days, [7, 30], true)) {
            $this->chartDays = $days;
        }
    }

    #[Computed]
    public function chartData(): array
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return $this->emptyChartData();
        }

        return Cache::remember(CacheHelper::dashboardChartKey($orgId, $this->chartDays), CacheHelper::TTL, function () use ($orgId) {
            $start = now()->subDays($this->chartDays)->startOfDay();
            $raw = DB::table('ticket_messages')
                ->join('tickets', 'ticket_messages.ticket_id', '=', 'tickets.id')
                ->where('tickets.organization_id', $orgId)
                ->where('ticket_messages.created_at', '>=', $start)
                ->select(DB::raw('DATE(ticket_messages.created_at) as day'), DB::raw('COUNT(*) as count'))
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('count', 'day')
                ->all();

            $jours = [
                __('pages.dashboard.weekday_sun'),
                __('pages.dashboard.weekday_mon'),
                __('pages.dashboard.weekday_tue'),
                __('pages.dashboard.weekday_wed'),
                __('pages.dashboard.weekday_thu'),
                __('pages.dashboard.weekday_fri'),
                __('pages.dashboard.weekday_sat'),
            ];
            $labels = array_map(function ($i) use ($jours) {
                $date = now()->subDays($this->chartDays - 1 - $i);

                return $this->chartDays === 7 ? $jours[$date->dayOfWeek] : $date->format('d/m');
            }, range(0, $this->chartDays - 1));
            $max = ! empty($raw) ? max($raw) : 1;
            $days = $this->chartDays;
            $values = [];
            for ($i = 0; $i < $days; $i++) {
                $d = now()->subDays($days - 1 - $i)->format('Y-m-d');
                $values[] = [
                    'label' => $labels[$i],
                    'count' => $raw[$d] ?? 0,
                    'pct' => $max > 0 ? min(100, (int) round((($raw[$d] ?? 0) / $max) * 100)) : 0,
                ];
            }

            return $values;
        });
    }

    private function emptyChartData(): array
    {
        $jours = [
            __('pages.dashboard.weekday_sun'),
            __('pages.dashboard.weekday_mon'),
            __('pages.dashboard.weekday_tue'),
            __('pages.dashboard.weekday_wed'),
            __('pages.dashboard.weekday_thu'),
            __('pages.dashboard.weekday_fri'),
            __('pages.dashboard.weekday_sat'),
        ];
        $labels = array_map(function ($i) use ($jours) {
            $date = now()->subDays($this->chartDays - 1 - $i);

            return $this->chartDays === 7 ? $jours[$date->dayOfWeek] : $date->format('d/m');
        }, range(0, $this->chartDays - 1));

        return array_map(fn ($label) => ['label' => $label, 'count' => 0, 'pct' => 0], $labels);
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 animate-pulse">
            <div class="h-4 bg-slate-200 rounded w-1/3 mb-4"></div>
            <div class="space-y-3">
                <div class="h-3 bg-slate-100 rounded w-full"></div>
                <div class="h-3 bg-slate-100 rounded w-5/6"></div>
                <div class="h-3 bg-slate-100 rounded w-4/6"></div>
            </div>
        </div>
        HTML;
    }

    public function render(): View
    {
        return view('livewire.dashboard.activity-chart');
    }
}
