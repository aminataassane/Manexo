<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Pagination\LengthAwarePaginator;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render()
    {
        $user = Auth::user();
        $orgId = session('current_organization_id');

        $tickets = ($user && $orgId)
            ? Ticket::query()
                ->with(['category', 'priority'])
                ->where('organization_id', $orgId)
                ->where('created_by', $user->id)
                ->latest()
                ->paginate(10)
            : new LengthAwarePaginator([], 0, 10);

        return view('livewire.tickets.index', [
            'tickets' => $tickets,
        ]);
    }
}
