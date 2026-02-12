<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Models\OrganizationMembership;
use App\Models\TicketFormField;
use App\Models\TicketFormTemplate;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.manexo-app')]
#[Title('Éditeur de formulaires')]
class FormBuilder extends Component
{
    public bool $canManageForms = false;

    public ?int $fb_selected_template_id = null;
    public ?int $fb_selected_field_id = null;
    public string $fb_selected_template_name = '';
    public ?int $fb_selected_template_category_id = null;
    public ?int $fb_selected_template_target_user_id = null;
    public bool $fb_selected_template_active = true;
    public string $fb_selected_field_key = '';
    public string $fb_selected_field_type = '';
    public string $fb_selected_field_label = '';
    public bool $fb_selected_field_required = false;
    public string $fb_selected_field_options = '';
    public string $fb_template_name = '';
    public ?int $fb_template_category_id = null;
    public ?int $fb_template_target_user_id = null;
    public bool $fb_template_active = true;
    public array $newFields = [];

    private function orgId(): int
    {
        return (int) session('current_organization_id');
    }

    private function currentRole(): string
    {
        $user = Auth::user();
        $orgId = $this->orgId();
        if (! $user instanceof \App\Models\User || ! $orgId) {
            return OrganizationRole::Member->value;
        }
        return (string) ($user->organizations()->whereKey($orgId)->first()?->pivot?->role ?? OrganizationRole::Member->value);
    }

    public function mount(): void
    {
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);
        $this->canManageForms = in_array($this->currentRole(), [OrganizationRole::Owner->value, OrganizationRole::Admin->value], true)
            || app()->environment('local');
        $this->fb_selected_template_id = TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->orderBy('name')
            ->value('id');
        $this->loadSelectedTemplate();
    }

    private function loadSelectedTemplate(): void
    {
        $orgId = $this->orgId();
        if (! $orgId || ! $this->fb_selected_template_id) {
            $this->fb_selected_template_name = '';
            $this->fb_selected_template_category_id = null;
            $this->fb_selected_template_target_user_id = null;
            $this->fb_selected_template_active = true;
            $this->fb_selected_field_id = null;
            $this->resetSelectedField();
            return;
        }
        $tpl = TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->fb_selected_template_id)
            ->first();
        if (! $tpl) {
            $this->fb_selected_template_id = null;
            $this->loadSelectedTemplate();
            return;
        }
        $this->fb_selected_template_name = (string) $tpl->name;
        $this->fb_selected_template_category_id = $tpl->ticket_category_id ? (int) $tpl->ticket_category_id : null;
        $this->fb_selected_template_target_user_id = $tpl->target_user_id ? (int) $tpl->target_user_id : null;
        $this->fb_selected_template_active = (bool) $tpl->is_active;
        $this->fb_selected_field_id = null;
        $this->resetSelectedField();
    }

    private function resetSelectedField(): void
    {
        $this->fb_selected_field_key = '';
        $this->fb_selected_field_type = '';
        $this->fb_selected_field_label = '';
        $this->fb_selected_field_required = false;
        $this->fb_selected_field_options = '';
    }

    public function selectTemplate(int $templateId): void
    {
        $this->fb_selected_template_id = $templateId;
        $this->loadSelectedTemplate();
    }

    public function selectField(int $fieldId): void
    {
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_template_id, 404);
        $field = TicketFormField::query()
            ->whereKey($fieldId)
            ->whereHas('template', fn ($q) => $q->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id))
            ->firstOrFail();
        $this->fb_selected_field_id = (int) $field->id;
        $this->fb_selected_field_key = (string) $field->key;
        $this->fb_selected_field_type = (string) $field->type;
        $this->fb_selected_field_label = (string) $field->label;
        $this->fb_selected_field_required = (bool) $field->required;
        $this->fb_selected_field_options = is_array($field->options) ? implode(', ', $field->options) : '';
    }

    public function saveSelectedTemplate(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_template_id, 404);
        $validated = $this->validate([
            'fb_selected_template_name' => ['required', 'string', 'max:120'],
            'fb_selected_template_category_id' => ['nullable', 'integer'],
            'fb_selected_template_target_user_id' => ['nullable', 'integer'],
            'fb_selected_template_active' => ['boolean'],
        ]);
        if (($validated['fb_selected_template_category_id'] ?? null) !== null) {
            if (! TicketCategory::query()->where('organization_id', $orgId)->whereKey((int) $validated['fb_selected_template_category_id'])->exists()) {
                $this->addError('fb_selected_template_category_id', 'Catégorie invalide.');
                return;
            }
        }
        if (($validated['fb_selected_template_target_user_id'] ?? null) !== null) {
            if (! OrganizationMembership::query()->where('organization_id', $orgId)->where('user_id', (int) $validated['fb_selected_template_target_user_id'])->exists()) {
                $this->addError('fb_selected_template_target_user_id', 'Utilisateur invalide.');
                return;
            }
        }
        TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->fb_selected_template_id)
            ->update([
                'name' => $validated['fb_selected_template_name'],
                'ticket_category_id' => $validated['fb_selected_template_category_id'] ?? null,
                'target_user_id' => $validated['fb_selected_template_target_user_id'] ?? null,
                'is_active' => (bool) ($validated['fb_selected_template_active'] ?? true),
            ]);
        $this->dispatch('toast', type: 'success', message: 'Formulaire enregistré.');
    }

    public function saveSelectedField(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_field_id || ! $this->fb_selected_template_id, 404);
        $validated = $this->validate([
            'fb_selected_field_label' => ['required', 'string', 'max:120'],
            'fb_selected_field_required' => ['boolean'],
            'fb_selected_field_options' => ['nullable', 'string', 'max:2000'],
        ]);
        $field = TicketFormField::query()
            ->whereKey((int) $this->fb_selected_field_id)
            ->whereHas('template', fn ($q) => $q->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id))
            ->firstOrFail();
        $options = $field->options;
        if ((string) $field->type === 'select') {
            $raw = trim((string) ($validated['fb_selected_field_options'] ?? ''));
            $list = collect(preg_split('/[\r\n,]+/', $raw))->map(fn ($v) => trim((string) $v))->filter()->values()->all();
            $options = count($list) ? $list : null;
        }
        $field->update([
            'label' => $validated['fb_selected_field_label'],
            'required' => (bool) ($validated['fb_selected_field_required'] ?? false),
            'options' => $options,
        ]);
        $this->dispatch('toast', type: 'success', message: 'Champ enregistré.');
    }

    public function quickAddField(string $type): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_template_id, 404);
        $tpl = TicketFormTemplate::query()->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id)->firstOrFail();
        $allowedTypes = ['text', 'textarea', 'select', 'checkbox', 'date', 'number', 'email'];
        if (! in_array($type, $allowedTypes, true)) {
            return;
        }
        $defaultLabel = match ($type) {
            'textarea' => 'Paragraphe',
            'select' => 'Sélection',
            'checkbox' => 'Case à cocher',
            'date' => 'Date',
            'number' => 'Nombre',
            'email' => 'Email',
            default => 'Texte court',
        };
        $key = Str::slug($defaultLabel, '_') ?: ('field_' . Str::lower(Str::random(6)));
        $key = Str::limit($key, 64, '');
        $baseKey = $key;
        $suffix = 2;
        while (TicketFormField::query()->where('template_id', $tpl->id)->where('key', $key)->exists()) {
            $key = Str::limit($baseKey . '_' . $suffix, 64, '');
            $suffix++;
        }
        $options = $type === 'select' ? ['Option 1', 'Option 2'] : null;
        $sortOrder = (int) (TicketFormField::query()->where('template_id', $tpl->id)->max('sort_order') ?? 0) + 10;
        $field = TicketFormField::query()->create([
            'template_id' => $tpl->id,
            'key' => $key,
            'label' => $defaultLabel,
            'type' => $type,
            'required' => false,
            'options' => $options,
            'sort_order' => $sortOrder,
        ]);
        $this->selectField((int) $field->id);
    }

    public function createFormTemplate(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $validated = $this->validate([
            'fb_template_name' => ['required', 'string', 'max:120'],
            'fb_template_category_id' => ['nullable', 'integer'],
            'fb_template_target_user_id' => ['nullable', 'integer'],
            'fb_template_active' => ['boolean'],
        ]);
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);
        if (($validated['fb_template_category_id'] ?? null) !== null) {
            if (! TicketCategory::query()->where('organization_id', $orgId)->whereKey((int) $validated['fb_template_category_id'])->exists()) {
                $this->addError('fb_template_category_id', 'Catégorie invalide.');
                return;
            }
        }
        if (($validated['fb_template_target_user_id'] ?? null) !== null) {
            if (! OrganizationMembership::query()->where('organization_id', $orgId)->where('user_id', (int) $validated['fb_template_target_user_id'])->exists()) {
                $this->addError('fb_template_target_user_id', 'Utilisateur invalide.');
                return;
            }
        }
        TicketFormTemplate::query()->create([
            'organization_id' => $orgId,
            'ticket_category_id' => $validated['fb_template_category_id'] ?? null,
            'name' => $validated['fb_template_name'],
            'request_type' => null,
            'target_user_id' => $validated['fb_template_target_user_id'] ?? null,
            'is_active' => (bool) ($validated['fb_template_active'] ?? true),
        ]);
        $this->fb_template_name = '';
        $this->fb_template_category_id = null;
        $this->fb_template_target_user_id = null;
        $this->fb_template_active = true;
        $this->dispatch('toast', type: 'success', message: 'Formulaire créé.');
    }

    public function deleteFormTemplate(int $templateId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);
        TicketFormTemplate::query()->where('organization_id', $orgId)->whereKey($templateId)->delete();
        if ($this->fb_selected_template_id === $templateId) {
            $this->fb_selected_template_id = TicketFormTemplate::query()->where('organization_id', $orgId)->orderBy('name')->value('id');
            $this->loadSelectedTemplate();
        }
        $this->dispatch('toast', type: 'success', message: 'Formulaire supprimé.');
    }

    public function deleteFormField(int $fieldId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);
        $field = TicketFormField::query()
            ->whereKey($fieldId)
            ->whereHas('template', fn ($q) => $q->where('organization_id', $orgId))
            ->firstOrFail();
        $field->delete();
        if ($this->fb_selected_field_id === $fieldId) {
            $this->fb_selected_field_id = null;
            $this->resetSelectedField();
        }
        $this->dispatch('toast', type: 'success', message: 'Champ supprimé.');
    }

    public function render()
    {
        $orgId = $this->orgId();
        $categories = $orgId ? TicketCategory::query()->where('organization_id', $orgId)->orderBy('name')->get(['id', 'name', 'is_active']) : collect();
        $members = $orgId ? OrganizationMembership::query()->where('organization_id', $orgId)->with(['user:id,name,email'])->orderBy('id')->get() : collect();
        $formTemplates = $orgId ? TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->with(['category:id,name', 'targetUser:id,name,email', 'fields:id,template_id,key,label,type,required,options,sort_order'])
            ->orderBy('name')
            ->get() : collect();

        return view('livewire.admin.form-builder', [
            'categories' => $categories,
            'members' => $members,
            'formTemplates' => $formTemplates,
        ]);
    }
}
