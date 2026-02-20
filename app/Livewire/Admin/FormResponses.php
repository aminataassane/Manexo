<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.manexo-app')]
#[Title('Réponses du formulaire')]
class FormResponses extends Component
{
    use WithPagination;

    public Form $form;
    public ?int $expandedResponseId = null;

    public function mount(Form $form): void
    {
        $orgId = (int) session('current_organization_id');
        abort_if(! $orgId || (int) $form->organization_id !== $orgId, 403);

        $user = Auth::user();
        $role = $user?->organizations()->whereKey($orgId)->first()?->pivot?->role ?? OrganizationRole::Member->value;
        $canView = in_array($role, [OrganizationRole::Owner->value, OrganizationRole::Admin->value], true)
            || app()->environment('local');
        abort_if(! $canView, 403);

        $this->form = $form;
    }

    public function toggleResponse(int $responseId): void
    {
        $this->expandedResponseId = $this->expandedResponseId === $responseId ? null : $responseId;
    }

    #[Computed]
    public function responses()
    {
        return FormResponse::query()
            ->where('form_id', $this->form->id)
            ->with(['user:id,name,email', 'assignment:id,status,due_date'])
            ->latest()
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.form-responses');
    }
}
