<?php

namespace App\Livewire\UserForms;

use App\Enums\FormAssignmentStatus;
use App\Models\FormAssignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.manexo-app')]
#[Title('Mes formulaires')]
class Index extends Component
{
    public string $tab = 'pending'; // pending | submitted | all

    #[Computed]
    public function orgId(): ?int
    {
        return (int) session('current_organization_id') ?: null;
    }

    #[Computed]
    public function assignments()
    {
        $userId = Auth::id();
        $orgId = $this->orgId;
        if (! $userId || ! $orgId) {
            return collect();
        }

        $query = FormAssignment::query()
            ->forUser($userId, $orgId)
            ->whereHas('form', fn($q) => $q->where('organization_id', $orgId))
            ->with(['form:id,name,description,status', 'assignedBy:id,name']);

        if ($this->tab === 'pending') {
            $query->whereIn('status', [FormAssignmentStatus::Pending, FormAssignmentStatus::Overdue]);
        } elseif ($this->tab === 'submitted') {
            $query->where('status', FormAssignmentStatus::Submitted);
        }

        return $query->latest()->get();
    }

    public function render()
    {
        return view('livewire.user-forms.index');
    }
}
