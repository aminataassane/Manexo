<?php

namespace App\Livewire\Tickets;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    public int $ticket_category_id;
    public int $ticket_priority_id;
    public string $subject = '';
    public string $description = '';

    public function mount(): void
    {
        $orgId = session('current_organization_id');

        // Preselect first active options if available
        if ($orgId) {
            $category = TicketCategory::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->first();

            $priority = TicketPriority::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('level')
                ->first();

            if ($category) {
                $this->ticket_category_id = $category->id;
            }

            if ($priority) {
                $this->ticket_priority_id = $priority->id;
            }
        }
    }

    public function submit(): void
    {
        $user = Auth::user();
        $orgId = session('current_organization_id');

        if (! $user || ! $orgId) {
            $this->redirectRoute('organizations.select', navigate: true);
            return;
        }

        $validated = $this->validate([
            'ticket_category_id' => ['required', 'integer', 'exists:ticket_categories,id'],
            'ticket_priority_id' => ['required', 'integer', 'exists:ticket_priorities,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        // Enforce scoping to current organization
        $categoryOk = TicketCategory::query()
            ->where('id', $validated['ticket_category_id'])
            ->where('organization_id', $orgId)
            ->exists();

        $priorityOk = TicketPriority::query()
            ->where('id', $validated['ticket_priority_id'])
            ->where('organization_id', $orgId)
            ->exists();

        if (! $categoryOk || ! $priorityOk) {
            $this->addError('ticket_category_id', 'Invalid selection for current organization.');
            return;
        }

        Ticket::create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'ticket_category_id' => $validated['ticket_category_id'],
            'ticket_priority_id' => $validated['ticket_priority_id'],
            'status' => TicketStatus::Open,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
        ]);

        $this->redirectRoute('tickets.index', navigate: true);
    }

    public function render()
    {
        $orgId = session('current_organization_id');

        $categories = $orgId
            ? TicketCategory::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
            : collect();

        $priorities = $orgId
            ? TicketPriority::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('level')
                ->get()
            : collect();

        return view('livewire.tickets.create', [
            'categories' => $categories,
            'priorities' => $priorities,
        ]);
    }
}
