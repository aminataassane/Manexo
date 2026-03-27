<?php

namespace App\Livewire\Dashboard;

use App\Enums\TicketMessageType;
use App\Helpers\CacheHelper;
use App\Livewire\Dashboard\Concerns\WithOrganization;
use App\Models\TicketMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RecentActivityPanel extends Component
{
    use WithOrganization;

    #[Computed]
    public function recentActivity(): \Illuminate\Support\Collection
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return collect();
        }

        return Cache::remember(CacheHelper::dashboardRecentActivityKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            $latestPerTicket = DB::table('ticket_messages')
                ->join('tickets', 'ticket_messages.ticket_id', '=', 'tickets.id')
                ->where('tickets.organization_id', $orgId)
                ->where('ticket_messages.type', TicketMessageType::Message->value)
                ->select('ticket_messages.ticket_id', DB::raw('MAX(ticket_messages.id) as max_id'))
                ->groupBy('ticket_messages.ticket_id')
                ->orderByRaw('MAX(ticket_messages.created_at) DESC')
                ->limit(8);

            return TicketMessage::query()
                ->joinSub($latestPerTicket, 'latest', fn ($join) => $join->on('ticket_messages.id', '=', 'latest.max_id'))
                ->with(['ticket:id,subject,public_id', 'user:id,name'])
                ->orderByDesc('ticket_messages.created_at')
                ->get()
                ->map(function (TicketMessage $m) {
                    $isYou = Auth::id() && (int) $m->user_id === (int) Auth::id();

                    return (object) [
                        'ticket_id' => $m->ticket_id,
                        'ticket_reference' => $m->ticket?->shortReference() ?? __('menu.tickets'),
                        'subject' => $m->ticket?->subject ?? __('menu.tickets'),
                        'user_name' => $isYou ? __('pages.dashboard.you') : ($m->user?->name ?? '—'),
                        'created_at' => $m->created_at,
                        'url' => $m->ticket?->public_id ? route('tickets.discussion', $m->ticket->public_id) : '#',
                    ];
                });
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
        return view('livewire.dashboard.recent-activity-panel');
    }
}
