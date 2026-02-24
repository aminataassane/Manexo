<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.manexo-app')]
#[Title('Réponses du formulaire')]
class FormResponses extends Component
{
    use WithPagination;

    public Form $form;

    #[Url(as: 'search')]
    public string $filterSearch = '';

    #[Url(as: 'source')]
    public string $filterSource = '';

    #[Url(as: 'from')]
    public string $filterDateFrom = '';

    #[Url(as: 'to')]
    public string $filterDateTo = '';

    public ?int $selectedResponseId = null;

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

    public function updatedFilterSearch(): void
    {
        $this->resetPage();
        $this->selectedResponseId = null;
    }

    public function updatedFilterSource(): void
    {
        $this->resetPage();
        $this->selectedResponseId = null;
    }

    public function updatedFilterDateFrom(): void
    {
        $this->resetPage();
        $this->selectedResponseId = null;
    }

    public function updatedFilterDateTo(): void
    {
        $this->resetPage();
        $this->selectedResponseId = null;
    }

    public function selectResponse(int $id): void
    {
        $this->selectedResponseId = $this->selectedResponseId === $id ? null : $id;
    }

    #[Computed]
    public function responses()
    {
        $query = FormResponse::query()
            ->where('form_id', $this->form->id)
            ->with(['user:id,name,email', 'assignment:id,status,due_date']);

        if ($this->filterSearch !== '') {
            $search = '%' . $this->filterSearch . '%';
            $query->where(function ($q) use ($search) {
                $q->where('respondent_name', 'ilike', $search)
                  ->orWhere('respondent_email', 'ilike', $search);
            });
        }

        if ($this->filterSource !== '') {
            $query->where('submitted_from', $this->filterSource);
        }

        if ($this->filterDateFrom !== '') {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo !== '') {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        return $query->latest()->paginate(20);
    }

    #[Computed]
    public function selectedResponse()
    {
        if (! $this->selectedResponseId) {
            return null;
        }

        return FormResponse::query()
            ->where('form_id', $this->form->id)
            ->where('id', $this->selectedResponseId)
            ->with(['user:id,name,email', 'assignment:id,status,due_date'])
            ->first();
    }

    public function exportCsv(): StreamedResponse
    {
        $form = $this->form;
        $formName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $form->name);

        return response()->streamDownload(function () use ($form) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            // Build header from field snapshot of latest response or form fields
            $fields = $form->fields->where('type', '!=', 'section');
            $headers = ['ID', 'Répondant', 'Email', 'Source', 'Date', 'Version', 'Sujet', 'Description'];
            foreach ($fields as $f) {
                $headers[] = $f->label;
            }
            fputcsv($handle, $headers, ';');

            // Stream rows
            FormResponse::query()
                ->where('form_id', $form->id)
                ->with('user:id,name,email')
                ->latest()
                ->chunk(100, function ($responses) use ($handle, $fields) {
                    foreach ($responses as $r) {
                        $baseFields = $r->base_fields ?? [];
                        $answers = $r->responses ?? [];
                        $row = [
                            $r->id,
                            $r->respondent_name ?? $r->user?->name ?? '',
                            $r->respondent_email ?? $r->user?->email ?? '',
                            $r->submitted_from ?? 'internal',
                            $r->created_at->format('d/m/Y H:i'),
                            'v' . $r->form_version,
                            $baseFields['subject'] ?? '',
                            $baseFields['description'] ?? '',
                        ];
                        foreach ($fields as $f) {
                            $val = $answers[$f->key] ?? '';
                            if (is_array($val)) {
                                if (($val['type'] ?? '') === 'file') {
                                    $val = $val['original_name'] ?? '(fichier)';
                                } else {
                                    $val = implode(', ', $val);
                                }
                            } elseif (is_bool($val)) {
                                $val = $val ? 'Oui' : 'Non';
                            }
                            $row[] = $val;
                        }
                        fputcsv($handle, $row, ';');
                    }
                });

            fclose($handle);
        }, "reponses-{$formName}.csv", [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.form-responses');
    }
}
