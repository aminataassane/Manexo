<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Models\OrganizationMembership;
use App\Models\TicketFormField;
use App\Models\TicketFormStep;
use App\Models\TicketFormTemplate;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public ?int $fb_selected_step_id = null;
    public string $fb_selected_template_name = '';
    public ?int $fb_selected_template_category_id = null;
    public ?int $fb_selected_template_target_user_id = null;
    public bool $fb_selected_template_active = true;
    public bool $fb_selected_template_public = false;
    public string $fb_selected_template_public_slug = '';
    public string $fb_selected_template_public_title = '';
    public string $fb_selected_template_public_description = '';
    public string $fb_selected_template_public_thank_you = '';
    public string $fb_selected_field_key = '';
    public string $fb_selected_field_type = '';
    public string $fb_selected_field_label = '';
    public string $fb_selected_field_placeholder = '';
    public string $fb_selected_field_help_text = '';
    public bool $fb_selected_field_required = false;
    public string $fb_selected_field_options = '';
    public string $fb_selected_step_title = '';
    public string $fb_selected_step_description = '';
    public string $fb_template_name = '';
    public ?int $fb_template_category_id = null;
    public ?int $fb_template_target_user_id = null;
    public bool $fb_template_active = true;
    public string $fb_step_title = '';
    public string $fb_step_description = '';
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
            $this->fb_selected_template_public = false;
            $this->fb_selected_template_public_slug = '';
            $this->fb_selected_template_public_title = '';
            $this->fb_selected_template_public_description = '';
            $this->fb_selected_template_public_thank_you = '';
            $this->fb_selected_field_id = null;
            $this->fb_selected_step_id = null;
            $this->resetSelectedStep();
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
        $this->fb_selected_template_public = (bool) ($tpl->is_public ?? false);
        $this->fb_selected_template_public_slug = (string) ($tpl->public_slug ?? '');
        $this->fb_selected_template_public_title = (string) ($tpl->public_title ?? '');
        $this->fb_selected_template_public_description = (string) ($tpl->public_description ?? '');
        $this->fb_selected_template_public_thank_you = (string) ($tpl->public_thank_you ?? '');
        $this->fb_selected_field_id = null;
        $this->resetSelectedField();

        $this->fb_selected_step_id = TicketFormStep::query()
            ->where('template_id', (int) $tpl->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');
        $this->loadSelectedStep();
    }

    private function resetSelectedField(): void
    {
        $this->fb_selected_field_key = '';
        $this->fb_selected_field_type = '';
        $this->fb_selected_field_label = '';
        $this->fb_selected_field_placeholder = '';
        $this->fb_selected_field_help_text = '';
        $this->fb_selected_field_required = false;
        $this->fb_selected_field_options = '';
    }

    private function resetSelectedStep(): void
    {
        $this->fb_selected_step_title = '';
        $this->fb_selected_step_description = '';
    }

    private function loadSelectedStep(): void
    {
        $orgId = $this->orgId();
        if (! $orgId || ! $this->fb_selected_template_id || ! $this->fb_selected_step_id) {
            $this->resetSelectedStep();
            return;
        }

        $step = TicketFormStep::query()
            ->whereKey((int) $this->fb_selected_step_id)
            ->whereHas('template', fn($q) => $q->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id))
            ->first();

        if (! $step) {
            $this->fb_selected_step_id = null;
            $this->resetSelectedStep();
            return;
        }

        $this->fb_selected_step_title = (string) ($step->title ?? '');
        $this->fb_selected_step_description = (string) ($step->description ?? '');
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
            ->whereHas('template', fn($q) => $q->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id))
            ->firstOrFail();
        $this->fb_selected_field_id = (int) $field->id;
        $this->fb_selected_field_key = (string) $field->key;
        $this->fb_selected_field_type = (string) $field->type;
        $this->fb_selected_field_label = (string) $field->label;
        $this->fb_selected_field_placeholder = (string) ($field->placeholder ?? '');
        $this->fb_selected_field_help_text = (string) ($field->help_text ?? '');
        $this->fb_selected_field_required = (bool) $field->required;
        $this->fb_selected_field_options = is_array($field->options) ? implode(', ', $field->options) : '';

        if ($field->step_id) {
            $this->fb_selected_step_id = (int) $field->step_id;
            $this->loadSelectedStep();
        }
    }

    public function selectStep(int $stepId): void
    {
        $this->fb_selected_step_id = $stepId;
        $this->fb_selected_field_id = null;
        $this->resetSelectedField();
        $this->loadSelectedStep();
        $this->dispatch('field-selected');
    }

    public function createStep(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_template_id, 404);

        $validated = $this->validate([
            'fb_step_title' => ['required', 'string', 'max:160'],
            'fb_step_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $tpl = TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->fb_selected_template_id)
            ->firstOrFail();

        $nextNumber = (int) (TicketFormStep::query()->where('template_id', $tpl->id)->max('number') ?? 0) + 1;
        $nextSort = (int) (TicketFormStep::query()->where('template_id', $tpl->id)->max('sort_order') ?? 0) + 10;

        $step = TicketFormStep::query()->create([
            'template_id' => $tpl->id,
            'number' => $nextNumber,
            'title' => trim((string) $validated['fb_step_title']),
            'description' => trim((string) ($validated['fb_step_description'] ?? '')) ?: null,
            'sort_order' => $nextSort,
        ]);

        $this->fb_step_title = '';
        $this->fb_step_description = '';

        $this->fb_selected_step_id = (int) $step->id;
        $this->loadSelectedStep();
        $this->dispatch('toast', type: 'success', message: 'Étape ajoutée.');
    }

    public function saveSelectedStep(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_template_id || ! $this->fb_selected_step_id, 404);

        $validated = $this->validate([
            'fb_selected_step_title' => ['required', 'string', 'max:160'],
            'fb_selected_step_description' => ['nullable', 'string', 'max:2000'],
        ]);

        TicketFormStep::query()
            ->whereKey((int) $this->fb_selected_step_id)
            ->whereHas('template', fn($q) => $q->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id))
            ->update([
                'title' => trim((string) $validated['fb_selected_step_title']),
                'description' => trim((string) ($validated['fb_selected_step_description'] ?? '')) ?: null,
            ]);

        $this->dispatch('toast', type: 'success', message: 'Étape enregistrée.');
    }

    public function deleteStep(int $stepId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_template_id, 404);

        $step = TicketFormStep::query()
            ->whereKey((int) $stepId)
            ->whereHas('template', fn($q) => $q->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id))
            ->firstOrFail();

        $otherStepId = TicketFormStep::query()
            ->where('template_id', (int) $step->template_id)
            ->where('id', '!=', (int) $step->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        if (! $otherStepId) {
            $this->dispatch('toast', type: 'error', message: 'Impossible de supprimer la dernière étape.');
            return;
        }

        DB::transaction(function () use ($step, $otherStepId) {
            TicketFormField::query()->where('step_id', (int) $step->id)->update(['step_id' => (int) $otherStepId]);
            $step->delete();
        });

        if ($this->fb_selected_step_id === (int) $stepId) {
            $this->fb_selected_step_id = (int) $otherStepId;
            $this->loadSelectedStep();
        }

        if ($this->fb_selected_field_id) {
            $fieldExists = TicketFormField::query()->whereKey((int) $this->fb_selected_field_id)->exists();
            if (! $fieldExists) {
                $this->fb_selected_field_id = null;
                $this->resetSelectedField();
            }
        }

        $this->dispatch('toast', type: 'success', message: 'Étape supprimée.');
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
            'fb_selected_template_public' => ['boolean'],
            'fb_selected_template_public_slug' => ['nullable', 'string', 'max:140'],
            'fb_selected_template_public_title' => ['nullable', 'string', 'max:160'],
            'fb_selected_template_public_description' => ['nullable', 'string', 'max:2000'],
            'fb_selected_template_public_thank_you' => ['nullable', 'string', 'max:2000'],
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

        $isPublic = (bool) ($validated['fb_selected_template_public'] ?? false);
        $slug = trim((string) ($validated['fb_selected_template_public_slug'] ?? ''));
        if ($isPublic) {
            if ($slug === '') {
                $this->addError('fb_selected_template_public_slug', 'Le slug est requis pour publier.');
                return;
            }
            $slug = Str::slug($slug);
            if ($slug === '') {
                $this->addError('fb_selected_template_public_slug', 'Slug invalide.');
                return;
            }
            $exists = TicketFormTemplate::query()
                ->whereNotNull('public_slug')
                ->where('public_slug', $slug)
                ->where('id', '!=', (int) $this->fb_selected_template_id)
                ->exists();
            if ($exists) {
                $this->addError('fb_selected_template_public_slug', 'Ce slug est déjà utilisé.');
                return;
            }
        } else {
            // If not public, clear the slug to avoid accidental sharing.
            $slug = '';
        }

        TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->fb_selected_template_id)
            ->update([
                'name' => $validated['fb_selected_template_name'],
                'ticket_category_id' => $validated['fb_selected_template_category_id'] ?? null,
                'target_user_id' => $validated['fb_selected_template_target_user_id'] ?? null,
                'is_active' => (bool) ($validated['fb_selected_template_active'] ?? true),
                'is_public' => $isPublic,
                'public_slug' => $slug !== '' ? $slug : null,
                'public_title' => trim((string) ($validated['fb_selected_template_public_title'] ?? '')) ?: null,
                'public_description' => trim((string) ($validated['fb_selected_template_public_description'] ?? '')) ?: null,
                'public_thank_you' => trim((string) ($validated['fb_selected_template_public_thank_you'] ?? '')) ?: null,
            ]);
        $this->dispatch('toast', type: 'success', message: 'Formulaire enregistré.');
    }

    public function generatePublicSlug(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $base = $this->fb_selected_template_name ?: 'formulaire';
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = 'formulaire';
        }
        // add small suffix to reduce collisions
        $slug = Str::limit($slug, 120, '');
        $candidate = $slug;
        $i = 2;
        while (TicketFormTemplate::query()->where('public_slug', $candidate)->exists()) {
            $candidate = Str::limit($slug . '-' . $i, 140, '');
            $i++;
        }
        $this->fb_selected_template_public_slug = $candidate;
        $this->fb_selected_template_public = true;
    }

    public function saveSelectedField(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_field_id || ! $this->fb_selected_template_id, 404);
        $validated = $this->validate([
            'fb_selected_field_key' => ['required', 'string', 'max:64', 'regex:/^[a-z][a-z0-9_]*$/i'],
            'fb_selected_field_label' => ['required', 'string', 'max:120'],
            'fb_selected_field_placeholder' => ['nullable', 'string', 'max:255'],
            'fb_selected_field_help_text' => ['nullable', 'string', 'max:2000'],
            'fb_selected_field_required' => ['boolean'],
            'fb_selected_field_options' => ['nullable', 'string', 'max:2000'],
        ]);
        $field = TicketFormField::query()
            ->whereKey((int) $this->fb_selected_field_id)
            ->whereHas('template', fn($q) => $q->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id))
            ->firstOrFail();

        $key = \Illuminate\Support\Str::lower(trim((string) $validated['fb_selected_field_key']));
        $key = \Illuminate\Support\Str::limit($key, 64, '');
        if ($key === '') {
            $this->addError('fb_selected_field_key', 'Clé invalide.');
            return;
        }
        $exists = TicketFormField::query()
            ->where('template_id', (int) $field->template_id)
            ->where('key', $key)
            ->where('id', '!=', (int) $field->id)
            ->exists();
        if ($exists) {
            $this->addError('fb_selected_field_key', 'Cette clé est déjà utilisée dans ce formulaire.');
            return;
        }

        $options = $field->options;
        if ((string) $field->type === 'select') {
            $raw = trim((string) ($validated['fb_selected_field_options'] ?? ''));
            $list = collect(preg_split('/[\r\n,]+/', $raw))->map(fn($v) => trim((string) $v))->filter()->values()->all();
            $options = count($list) ? $list : null;
        }
        $field->update([
            'key' => $key,
            'label' => $validated['fb_selected_field_label'],
            'placeholder' => trim((string) ($validated['fb_selected_field_placeholder'] ?? '')) ?: null,
            'help_text' => trim((string) ($validated['fb_selected_field_help_text'] ?? '')) ?: null,
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
        $stepId = $this->fb_selected_step_id
            ?: TicketFormStep::query()->where('template_id', (int) $tpl->id)->orderBy('sort_order')->orderBy('id')->value('id');
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
        $sortOrder = $stepId
            ? ((int) (TicketFormField::query()->where('step_id', (int) $stepId)->max('sort_order') ?? 0) + 10)
            : ((int) (TicketFormField::query()->where('template_id', (int) $tpl->id)->max('sort_order') ?? 0) + 10);
        $field = TicketFormField::query()->create([
            'template_id' => $tpl->id,
            'step_id' => $stepId ? (int) $stepId : null,
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
        $tpl = TicketFormTemplate::query()->create([
            'organization_id' => $orgId,
            'ticket_category_id' => $validated['fb_template_category_id'] ?? null,
            'name' => $validated['fb_template_name'],
            'request_type' => null,
            'target_user_id' => $validated['fb_template_target_user_id'] ?? null,
            'is_active' => (bool) ($validated['fb_template_active'] ?? true),
        ]);
        TicketFormStep::query()->create([
            'template_id' => (int) $tpl->id,
            'number' => 1,
            'title' => 'Informations',
            'description' => null,
            'sort_order' => 10,
        ]);
        $this->fb_selected_template_id = (int) $tpl->id;
        $this->loadSelectedTemplate();
        $this->fb_template_name = '';
        $this->fb_template_category_id = null;
        $this->fb_template_target_user_id = null;
        $this->fb_template_active = true;
        $this->dispatch('toast', type: 'success', message: 'Formulaire créé.');
    }

    public function duplicateSelectedTemplate(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_template_id, 404);

        $src = TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->with(['steps.fields'])
            ->whereKey((int) $this->fb_selected_template_id)
            ->firstOrFail();

        DB::transaction(function () use ($src, $orgId) {
            $new = TicketFormTemplate::query()->create([
                'organization_id' => $orgId,
                'ticket_category_id' => $src->ticket_category_id,
                'name' => (string) $src->name . ' (Copie)',
                'request_type' => $src->request_type,
                'target_user_id' => $src->target_user_id,
                'is_active' => false,
                'is_public' => false,
                'public_slug' => null,
                'public_title' => null,
                'public_description' => null,
                'public_thank_you' => null,
            ]);

            $stepMap = [];
            foreach ($src->steps as $step) {
                $newStep = TicketFormStep::query()->create([
                    'template_id' => (int) $new->id,
                    'number' => (int) $step->number,
                    'title' => (string) $step->title,
                    'description' => $step->description,
                    'sort_order' => (int) $step->sort_order,
                ]);
                $stepMap[(int) $step->id] = (int) $newStep->id;
            }

            // Fallback if old template somehow has no steps
            if (count($stepMap) === 0) {
                $newStep = TicketFormStep::query()->create([
                    'template_id' => (int) $new->id,
                    'number' => 1,
                    'title' => 'Informations',
                    'description' => null,
                    'sort_order' => 10,
                ]);
                $stepMap[0] = (int) $newStep->id;
            }

            foreach ($src->steps as $step) {
                foreach ($step->fields as $field) {
                    TicketFormField::query()->create([
                        'template_id' => (int) $new->id,
                        'step_id' => (int) ($stepMap[(int) $step->id] ?? $stepMap[0] ?? null),
                        'key' => (string) $field->key,
                        'label' => (string) $field->label,
                        'type' => (string) $field->type,
                        'required' => (bool) $field->required,
                        'options' => $field->options,
                        'sort_order' => (int) $field->sort_order,
                    ]);
                }
            }

            $this->fb_selected_template_id = (int) $new->id;
        });

        $this->loadSelectedTemplate();
        $this->dispatch('toast', type: 'success', message: 'Formulaire dupliqué.');
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
            ->whereHas('template', fn($q) => $q->where('organization_id', $orgId))
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
            ->with([
                'category:id,name',
                'targetUser:id,name,email',
                'steps:id,template_id,number,title,description,sort_order',
                'steps.fields:id,template_id,step_id,key,label,type,required,options,sort_order',
                'fields:id,template_id,step_id,key,label,type,required,options,sort_order',
            ])
            ->orderBy('name')
            ->get() : collect();

        return view('livewire.admin.form-builder', [
            'categories' => $categories,
            'members' => $members,
            'formTemplates' => $formTemplates,
        ]);
    }
}
