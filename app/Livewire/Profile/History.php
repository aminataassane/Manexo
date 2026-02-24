<?php

namespace App\Livewire\Profile;

use App\Models\FormResponse;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
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

    public function render()
    {
        $userId = Auth::id();
        abort_if(! $userId, 403);

        $type = in_array($this->type, ['all', 'tickets', 'assignations', 'forms'], true) ? $this->type : 'all';

        $isPgsql = DB::connection()->getDriverName() === 'pgsql';
        $nullBigint = $isPgsql ? 'null::bigint' : 'CAST(null AS UNSIGNED)';

        $created = DB::table('tickets')
            ->where('created_by', $userId)
            ->selectRaw("'tickets' as event_type, id as ticket_id, subject, status, created_at as at, {$nullBigint} as form_id, {$nullBigint} as form_response_id");

        $assigned = DB::table('tickets')
            ->where('assigned_to', $userId)
            ->selectRaw("'assignations' as event_type, id as ticket_id, subject, status, updated_at as at, {$nullBigint} as form_id, {$nullBigint} as form_response_id");

        $forms = DB::table('form_responses')
            ->where('form_responses.user_id', $userId)
            ->join('forms', 'form_responses.form_id', '=', 'forms.id')
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

        $stats = $this->computeStats($userId);

        return view('livewire.profile.history', [
            'events' => $events,
            'type' => $type,
            'stats' => $stats,
        ]);
    }

    /**
     * @return array{total_created: int, total_assigned: int, total_forms: int, this_week: int, this_month: int, by_status: array<string, int>}
     */
    private function computeStats(int $userId): array
    {
        $now = now();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();

        $totalCreated = Ticket::query()->where('created_by', $userId)->count();
        $totalAssigned = Ticket::query()->where('assigned_to', $userId)->count();
        $totalForms = FormResponse::query()->where('user_id', $userId)->count();

        $ticketsThisWeek = (int) DB::table('tickets')
            ->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)->orWhere('assigned_to', $userId);
            })
            ->where(function ($q) use ($startOfWeek) {
                $q->where('created_at', '>=', $startOfWeek)
                    ->orWhere('updated_at', '>=', $startOfWeek);
            })
            ->selectRaw('count(distinct id) as c')
            ->value('c');
        $formsThisWeek = (int) DB::table('form_responses')
            ->where('user_id', $userId)
            ->where('created_at', '>=', $startOfWeek)
            ->count();
        $thisWeek = $ticketsThisWeek + $formsThisWeek;

        $ticketsThisMonth = (int) DB::table('tickets')
            ->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)->orWhere('assigned_to', $userId);
            })
            ->where(function ($q) use ($startOfMonth) {
                $q->where('created_at', '>=', $startOfMonth)
                    ->orWhere('updated_at', '>=', $startOfMonth);
            })
            ->selectRaw('count(distinct id) as c')
            ->value('c');
        $formsThisMonth = (int) DB::table('form_responses')
            ->where('user_id', $userId)
            ->where('created_at', '>=', $startOfMonth)
            ->count();
        $thisMonth = $ticketsThisMonth + $formsThisMonth;

        $byStatus = DB::table('tickets')
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
    }
}

