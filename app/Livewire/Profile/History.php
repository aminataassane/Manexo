<?php

namespace App\Livewire\Profile;

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

    /** @var string all|tickets|assignations */
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

        $type = in_array($this->type, ['all', 'tickets', 'assignations'], true) ? $this->type : 'all';

        $created = DB::table('tickets')
            ->where('created_by', $userId)
            ->selectRaw("'tickets' as type, 'Ticket créé' as label, id as ticket_id, subject, status, created_at as at");

        $assigned = DB::table('tickets')
            ->where('assigned_to', $userId)
            ->selectRaw("'assignations' as type, 'Ticket assigné à vous' as label, id as ticket_id, subject, status, updated_at as at");

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
        } else {
            $union = $created->unionAll($assigned);

            $events = DB::query()
                ->fromSub($union, 'events')
                ->orderByDesc('at')
                ->paginate(20);
        }

        return view('livewire.profile.history', [
            'events' => $events,
            'type' => $type,
        ]);
    }
}

