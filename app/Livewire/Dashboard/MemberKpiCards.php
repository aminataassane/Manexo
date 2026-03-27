<?php

namespace App\Livewire\Dashboard;

use App\Helpers\CacheHelper;
use App\Livewire\Dashboard\Concerns\WithOrganization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MemberKpiCards extends Component
{
    use WithOrganization;

    #[Computed]
    public function myKpis(): array
    {
        $orgId = $this->orgId;
        $userId = Auth::id();
        if (! $orgId || ! $userId) {
            return ['my_open' => 0, 'my_in_progress' => 0, 'my_total' => 0, 'my_resolved' => 0];
        }

        return Cache::remember("dashboard:my_kpis:{$orgId}:{$userId}", CacheHelper::TTL, function () use ($orgId, $userId) {
            $row = DB::table('tickets')
                ->where('organization_id', $orgId)
                ->where('created_by', $userId)
                ->select([
                    DB::raw("COUNT(*) FILTER (WHERE status = 'open') as my_open"),
                    DB::raw("COUNT(*) FILTER (WHERE status = 'in_progress') as my_in_progress"),
                    DB::raw('COUNT(*) as my_total'),
                    DB::raw("COUNT(*) FILTER (WHERE status IN ('resolved','closed')) as my_resolved"),
                ])
                ->first();

            return [
                'my_open' => (int) ($row->my_open ?? 0),
                'my_in_progress' => (int) ($row->my_in_progress ?? 0),
                'my_total' => (int) ($row->my_total ?? 0),
                'my_resolved' => (int) ($row->my_resolved ?? 0),
            ];
        });
    }

    public function render(): View
    {
        return view('livewire.dashboard.member-kpi-cards');
    }
}
