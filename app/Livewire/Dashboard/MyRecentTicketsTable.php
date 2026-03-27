<?php

namespace App\Livewire\Dashboard;

use App\Helpers\CacheHelper;
use App\Livewire\Dashboard\Concerns\WithOrganization;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MyRecentTicketsTable extends Component
{
    use WithOrganization;

    #[Computed]
    public function myRecentTickets(): \Illuminate\Support\Collection
    {
        $orgId = $this->orgId;
        $userId = Auth::id();
        if (! $orgId || ! $userId) {
            return collect();
        }

        return Cache::remember("dashboard:my_tickets:{$orgId}:{$userId}", CacheHelper::TTL, function () use ($orgId, $userId) {
            return Ticket::query()
                ->where('tickets.organization_id', $orgId)
                ->where(fn ($q) => $q->where('tickets.created_by', $userId)->orWhere('tickets.assigned_to', $userId))
                ->leftJoin('ticket_priorities', 'tickets.ticket_priority_id', '=', 'ticket_priorities.id')
                ->select('tickets.*', 'ticket_priorities.name as priority_name', 'ticket_priorities.level as priority_level')
                ->orderByDesc('tickets.updated_at')
                ->limit(7)
                ->get();
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
        return view('livewire.dashboard.my-recent-tickets-table', [
            'statusLabel' => function (string $status): string {
                return __('tickets.status.'.$status);
            },
            'statusPill' => function (string $status): array {
                return match ($status) {
                    'open' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'icon' => 'solar:danger-circle-bold'],
                    'in_progress' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'icon' => 'solar:clock-circle-bold'],
                    'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'icon' => 'solar:pause-circle-bold'],
                    'resolved', 'closed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'icon' => 'solar:check-circle-bold'],
                    default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'solar:info-circle-bold'],
                };
            },
        ]);
    }
}
