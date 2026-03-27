<?php

namespace App\Livewire\Dashboard;

use App\Enums\FormAssignmentStatus;
use App\Helpers\CacheHelper;
use App\Livewire\Dashboard\Concerns\WithOrganization;
use App\Models\FormAssignment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PendingFormsPanel extends Component
{
    use WithOrganization;

    #[Computed]
    public function pendingFormAssignments(): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        $orgId = $this->orgId;
        if (! $user || ! $orgId) {
            return collect();
        }

        $userId = (int) $user->id;

        return Cache::remember(CacheHelper::dashboardPendingFormsKey($orgId, $userId), CacheHelper::TTL, function () use ($userId, $orgId) {
            return FormAssignment::query()
                ->forUser($userId, $orgId)
                ->whereIn('status', [FormAssignmentStatus::Pending, FormAssignmentStatus::Overdue])
                ->with(['form:id,name'])
                ->orderBy('due_date')
                ->limit(5)
                ->get();
        });
    }

    #[Computed]
    public function pendingFormsCount(): int
    {
        $user = Auth::user();
        $orgId = $this->orgId;
        if (! $user || ! $orgId) {
            return 0;
        }

        $userId = (int) $user->id;

        return Cache::remember(CacheHelper::dashboardPendingFormsCountKey($orgId, $userId), CacheHelper::TTL, function () use ($userId, $orgId) {
            return FormAssignment::query()
                ->forUser($userId, $orgId)
                ->whereIn('status', [FormAssignmentStatus::Pending, FormAssignmentStatus::Overdue])
                ->count();
        });
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
        return view('livewire.dashboard.pending-forms-panel');
    }
}
