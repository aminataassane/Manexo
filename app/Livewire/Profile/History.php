<?php

namespace App\Livewire\Profile;

use App\Models\FormResponse;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.manexo-app')]
#[Title('Historique')]
class History extends Component
{
    use WithPagination;

    /** @var string all|tickets|assignations|forms */
    public string $type = 'all';

    protected $queryString = [
        'type' => ['except' => 'all'],
        'page' => ['except' => 1],
    ];

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function setType(string $type): void
    {
        $allowed = ['all', 'tickets', 'assignations', 'forms'];
        if (! in_array($type, $allowed, true)) {
            return;
        }
        if ($this->type === $type) {
            return;
        }

        $this->type = $type;
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();
        abort_if(! $userId, 403);
        $orgId = (int) session('current_organization_id');
        abort_if(! $orgId, 403);

        $type = in_array($this->type, ['all', 'tickets', 'assignations', 'forms'], true) ? $this->type : 'all';

        $isPgsql = DB::connection()->getDriverName() === 'pgsql';
        $nullBigint = $isPgsql ? 'null::bigint' : 'CAST(null AS UNSIGNED)';

        $created = DB::table('tickets')
            ->where('organization_id', $orgId)
            ->where('created_by', $userId)
            ->selectRaw("'tickets' as event_type, id as ticket_id, subject, status, created_at as at, {$nullBigint} as form_id, {$nullBigint} as form_response_id");

        $assigned = DB::table('tickets')
            ->where('organization_id', $orgId)
            ->where('assigned_to', $userId)
            ->selectRaw("'assignations' as event_type, id as ticket_id, subject, status, updated_at as at, {$nullBigint} as form_id, {$nullBigint} as form_response_id");

        $forms = DB::table('form_responses')
            ->where('form_responses.user_id', $userId)
            ->join('forms', 'form_responses.form_id', '=', 'forms.id')
            ->where('forms.organization_id', $orgId)
            ->selectRaw("'forms' as event_type, {$nullBigint} as ticket_id, forms.name as subject, 'soumis' as status, form_responses.created_at as at, form_responses.form_id, form_responses.id as form_response_id");

        if ($type === 'tickets') {
            $events = DB::query()
                ->fromSub($created, 'events')
                ->orderByDesc('at')
                ->paginate(20);
        } elseif ($type === 'assignations') {
            $events = DB::query()
                ->fromSub($assigned, 'events')
                ->orderByDesc('at')
                ->paginate(20);
        } elseif ($type === 'forms') {
            $events = DB::query()
                ->fromSub($forms, 'events')
                ->orderByDesc('at')
                ->paginate(20);
        } else {
            $union = $created->unionAll($assigned)->unionAll($forms);
            $events = DB::query()
                ->fromSub($union, 'events')
                ->orderByDesc('at')
                ->paginate(20);
        }

        $stats = $this->computeStats($userId, $orgId);

        return view('livewire.profile.history', [
            'events' => $events,
            'type' => $type,
            'stats' => $stats,
        ]);
    }

    /**
     * @return array{total_created: int, total_assigned: int, total_forms: int, this_week: int, this_month: int, by_status: array<string, int>}
     */
    private function computeStats(int $userId, int $orgId): array
    {
        return Cache::remember("profile_history_stats:{$orgId}:{$userId}", 120, function () use ($userId, $orgId) {
        $now = now();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();

        $ticketAgg = DB::table('tickets')
            ->where('organization_id', $orgId)
            ->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)->orWhere('assigned_to', $userId);
            })
            ->selectRaw('sum(case when created_by = ? then 1 else 0 end) as total_created', [$userId])
            ->selectRaw('sum(case when assigned_to = ? then 1 else 0 end) as total_assigned', [$userId])
            ->selectRaw('sum(case when (created_at >= ? or updated_at >= ?) then 1 else 0 end) as tickets_this_week', [$startOfWeek, $startOfWeek])
            ->selectRaw('sum(case when (created_at >= ? or updated_at >= ?) then 1 else 0 end) as tickets_this_month', [$startOfMonth, $startOfMonth])
            ->first();

        $formAgg = DB::table('form_responses')
            ->join('forms', 'form_responses.form_id', '=', 'forms.id')
            ->where('forms.organization_id', $orgId)
            ->where('form_responses.user_id', $userId)
            ->selectRaw('count(*) as total_forms')
            ->selectRaw('sum(case when form_responses.created_at >= ? then 1 else 0 end) as forms_this_week', [$startOfWeek])
            ->selectRaw('sum(case when form_responses.created_at >= ? then 1 else 0 end) as forms_this_month', [$startOfMonth])
            ->first();

        $totalCreated = (int) ($ticketAgg->total_created ?? 0);
        $totalAssigned = (int) ($ticketAgg->total_assigned ?? 0);
        $totalForms = (int) ($formAgg->total_forms ?? 0);
        $thisWeek = (int) ($ticketAgg->tickets_this_week ?? 0) + (int) ($formAgg->forms_this_week ?? 0);
        $thisMonth = (int) ($ticketAgg->tickets_this_month ?? 0) + (int) ($formAgg->forms_this_month ?? 0);

        $byStatus = DB::table('tickets')
            ->where('organization_id', $orgId)
            ->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)->orWhere('assigned_to', $userId);
            })
            ->selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->all();

        return [
            'total_created' => $totalCreated,
            'total_assigned' => $totalAssigned,
            'total_forms' => $totalForms,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
            'by_status' => $byStatus,
        ];
        });
    }
}

