<?php

namespace App\Livewire\Dashboard;

use App\Helpers\CacheHelper;
use App\Livewire\Dashboard\Concerns\WithOrganization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AdminKpiCards extends Component
{
    use WithOrganization;

    #[Computed]
    public function kpis(): array
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return ['open' => 0, 'in_progress' => 0, 'pending' => 0, 'resolved7d' => 0];
        }

        return Cache::remember(CacheHelper::dashboardKpisKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            $sevenDaysAgo = now()->subDays(7)->toDateTimeString();
            $row = DB::table('tickets')
                ->where('organization_id', $orgId)
                ->selectRaw("
                    COUNT(*) FILTER (WHERE status = 'open') as open_count,
                    COUNT(*) FILTER (WHERE status = 'in_progress') as in_progress_count,
                    COUNT(*) FILTER (WHERE status = 'pending') as pending_count,
                    COUNT(*) FILTER (WHERE status IN ('resolved','closed') AND updated_at >= ?) as resolved7d_count
                ", [$sevenDaysAgo])
                ->first();

            return [
                'open' => (int) ($row->open_count ?? 0),
                'in_progress' => (int) ($row->in_progress_count ?? 0),
                'pending' => (int) ($row->pending_count ?? 0),
                'resolved7d' => (int) ($row->resolved7d_count ?? 0),
            ];
        });
    }

    public function render(): View
    {
        return view('livewire.dashboard.admin-kpi-cards');
    }
}
