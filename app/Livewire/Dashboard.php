<?php

namespace App\Livewire;

use App\Enums\FormAssignmentStatus;
use App\Enums\Permission;
use App\Enums\TicketMessageType;
use App\Models\DiscussionMessage;
use App\Models\DiscussionThread;
use App\Models\FormAssignment;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manexo-app', ['title' => 'pages.dashboard.title'])]
class Dashboard extends Component
{
    /** 7 or 30 for chart range. */
    public int $chartDays = 7;

    public function setChartDays(int $days): void
    {
        if (in_array($days, [7, 30], true)) {
            $this->chartDays = $days;
        }
    }

    #[Computed]
    public function organization(): ?\App\Models\Organization
    {
        $org = request()->attributes->get('currentOrganization');
        if ($org !== null) {
            return $org;
        }
        $orgId = session('current_organization_id');
        if (! $orgId) {
            return null;
        }
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user) {
            return \App\Models\Organization::find($orgId);
        }
        // Load via user's organizations so pivot (role) is present (needed for isAdminView)
        return $user->organizations()->whereKey($orgId)->first();
    }

    #[Computed]
    public function role(): string
    {
        $org = $this->organization;
        if (! $org) {
            return 'member';
        }
        return (string) ($org->pivot?->role ?? 'member');
    }

    #[Computed]
    public function isAdminView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->hasAnyPermission([
            Permission::TicketsViewAll,
            Permission::ReportsView,
            Permission::TeamInvite,
        ]);
    }

    #[Computed]
    public function orgId(): ?int
    {
        return $this->organization?->id;
    }

    /** KPI counts — single aggregated query instead of 4 separate COUNTs. */
    #[Computed]
    public function kpis(): array
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return ['open' => 0, 'in_progress' => 0, 'pending' => 0, 'resolved7d' => 0];
        }

        return Cache::remember(CacheHelper::dashboardKpisKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            $row = DB::table('tickets')
                ->where('organization_id', $orgId)
                ->select([
                    DB::raw("COUNT(*) FILTER (WHERE status = 'open') as open_count"),
                    DB::raw("COUNT(*) FILTER (WHERE status = 'in_progress') as in_progress_count"),
                    DB::raw("COUNT(*) FILTER (WHERE status = 'pending') as pending_count"),
                    DB::raw("COUNT(*) FILTER (WHERE status IN ('resolved','closed') AND updated_at >= '" . now()->subDays(7)->toDateTimeString() . "') as resolved7d_count"),
                ])
                ->first();

            return [
                'open' => (int) ($row->open_count ?? 0),
                'in_progress' => (int) ($row->in_progress_count ?? 0),
                'pending' => (int) ($row->pending_count ?? 0),
                'resolved7d' => (int) ($row->resolved7d_count ?? 0),
            ];
        });
    }

    /** Chart data: activity per day — uses JOIN instead of whereHas. */
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

    /** Priority tickets — JOIN for ordering, skip redundant eager load. */
    #[Computed]
    public function priorityTickets()
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return collect();
        }

        return Cache::remember(CacheHelper::dashboardPriorityTicketsKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            return Ticket::query()
                ->where('tickets.organization_id', $orgId)
                ->whereIn('tickets.status', ['open', 'in_progress', 'pending'])
                ->join('ticket_priorities', 'tickets.ticket_priority_id', '=', 'ticket_priorities.id')
                ->orderByDesc('ticket_priorities.level')
                ->orderByDesc('tickets.updated_at')
                ->select('tickets.*', 'ticket_priorities.name as priority_name', 'ticket_priorities.level as priority_level')
                ->limit(5)
                ->get();
        });
    }

    /** Recent discussion threads with last message. */
    #[Computed]
    public function recentDiscussions(): \Illuminate\Support\Collection
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return collect();
        }

        return Cache::remember(CacheHelper::dashboardRecentDiscussionsKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            $threads = DiscussionThread::query()
                ->where('organization_id', $orgId)
                ->active()
                ->with(['messages' => fn ($q) => $q->latest('created_at')->limit(1), 'messages.user:id,name'])
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get();

            return $threads
                ->map(function (DiscussionThread $t) {
                    $last = $t->messages->first();
                    return (object) [
                        'id' => $t->id,
                        'name' => $t->name,
                        'last_body' => $last ? Str::limit($last->body, 50) : null,
                        'last_at' => $last?->created_at,
                        'last_user_name' => $last?->user?->name,
                        'url' => route('discussions.index', ['ticket' => 'd-' . $t->id]),
                    ];
                })
                ->sortByDesc('last_at')
                ->values();
        });
    }

    /** Unread-like count: threads with at least one message in last 24h. */
    #[Computed]
    public function discussionsUnreadCount(): int
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return 0;
        }

        return Cache::remember(CacheHelper::dashboardDiscussionsUnreadKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            return DiscussionThread::query()
                ->where('organization_id', $orgId)
                ->active()
                ->whereHas('messages', fn ($q) => $q->where('created_at', '>=', now()->subDay()))
                ->count();
        });
    }

    /** Recent activity — uses JOIN instead of whereHas, SQL-level dedup. */
    #[Computed]
    public function recentActivity(): \Illuminate\Support\Collection
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return collect();
        }

        return Cache::remember(CacheHelper::dashboardRecentActivityKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            // Use a subquery to get the latest message per ticket (SQL-level dedup)
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
                ->with(['ticket:id,subject', 'user:id,name'])
                ->orderByDesc('ticket_messages.created_at')
                ->get()
                ->map(function (TicketMessage $m) {
                    $isYou = Auth::id() && (int) $m->user_id === (int) Auth::id();
                    return (object) [
                        'ticket_id' => $m->ticket_id,
                        'subject' => $m->ticket?->subject ?? __('menu.tickets') . ' #' . $m->ticket_id,
                        'user_name' => $isYou ? __('pages.dashboard.you') : ($m->user?->name ?? '—'),
                        'created_at' => $m->created_at,
                        'url' => route('tickets.discussion', $m->ticket_id),
                    ];
                });
        });
    }

    /** Pending form assignments for the current user. */
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

    public function render()
    {
        return view('livewire.dashboard', [
            'statusLabel' => function (string $status): string {
                return __('tickets.status.' . $status);
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
