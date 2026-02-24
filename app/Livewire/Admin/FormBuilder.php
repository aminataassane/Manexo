<?php

namespace App\Livewire\Admin;

use App\Enums\FormAssignmentStatus;
use App\Enums\FormStatus;
use App\Enums\OrganizationRole;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Models\Form;
use App\Models\FormAssignment;
use App\Models\FormField;
use App\Models\OrganizationFunction;
use App\Models\OrganizationMembership;
use App\Models\TicketCategory;
use App\Notifications\FormAssignmentNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    // Active tab: champs | assignations | reponses
    public string $activeTab = 'champs';

    // Form selection
    public ?int $fb_selected_form_id = null;
    public string $fb_selected_form_name = '';
    public ?int $fb_selected_form_category_id = null;
    public ?int $fb_selected_form_target_user_id = null;
    public string $fb_selected_form_status = 'draft';
    public bool $fb_selected_form_public = false;
    public bool $fb_selected_form_creates_ticket = true;
    public string $fb_selected_form_slug = '';
    public string $fb_selected_form_public_title = '';
    public string $fb_selected_form_public_description = '';
    public string $fb_selected_form_public_thank_you = '';
    public string $fb_selected_form_description = '';

    // Field selection
    public ?int $fb_selected_field_id = null;
    public string $fb_selected_field_key = '';
    public string $fb_selected_field_type = '';
    public string $fb_selected_field_label = '';
    public string $fb_selected_field_placeholder = '';
    public string $fb_selected_field_help_text = '';
    public bool $fb_selected_field_required = false;
    public string $fb_selected_field_layout = 'full';
    public string $fb_selected_field_display_mode = 'list';
    /** @var array<int, string> Options for select/radio/checkbox (one entry per option) */
    public array $fb_selected_field_options_list = [];

    // New form
    public string $fb_form_name = '';

    // Assignment form
    public ?int $assign_user_id = null;
    public ?int $assign_function_id = null;
    public ?string $assign_due_date = null;

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
        $this->fb_selected_form_id = Form::query()
            ->forOrg($orgId)
            ->orderBy('name')
            ->value('id');
        $this->loadSelectedForm();
    }

    private function loadSelectedForm(): void
    {
        $orgId = $this->orgId();
        if (! $orgId || ! $this->fb_selected_form_id) {
            $this->fb_selected_form_name = '';
            $this->fb_selected_form_category_id = null;
            $this->fb_selected_form_target_user_id = null;
            $this->fb_selected_form_status = 'draft';
            $this->fb_selected_form_public = false;
            $this->fb_selected_form_creates_ticket = true;
            $this->fb_selected_form_slug = '';
            $this->fb_selected_form_public_title = '';
            $this->fb_selected_form_public_description = '';
            $this->fb_selected_form_public_thank_you = '';
            $this->fb_selected_form_description = '';
            $this->fb_selected_field_id = null;
            $this->resetSelectedField();
            return;
        }
        $form = Form::query()
            ->forOrg($orgId)
            ->whereKey((int) $this->fb_selected_form_id)
            ->first();
        if (! $form) {
            $this->fb_selected_form_id = null;
            $this->loadSelectedForm();
            return;
        }
        $this->fb_selected_form_name = (string) $form->name;
        $this->fb_selected_form_category_id = $form->ticket_category_id ? (int) $form->ticket_category_id : null;
        $this->fb_selected_form_target_user_id = $form->target_user_id ? (int) $form->target_user_id : null;
        $this->fb_selected_form_status = $form->status instanceof FormStatus ? $form->status->value : (string) $form->status;
        $this->fb_selected_form_public = (bool) $form->is_public;
        $this->fb_selected_form_creates_ticket = (bool) ($form->creates_ticket ?? true);
        $this->fb_selected_form_slug = (string) ($form->slug ?? '');
        $this->fb_selected_form_public_title = (string) ($form->public_title ?? '');
        $this->fb_selected_form_public_description = (string) ($form->public_description ?? '');
        $this->fb_selected_form_public_thank_you = (string) ($form->public_thank_you ?? '');
        $this->fb_selected_form_description = (string) ($form->description ?? '');
        $this->fb_selected_field_id = null;
        $this->resetSelectedField();
    }

    private function resetSelectedField(): void
    {
        $this->fb_selected_field_key = '';
        $this->fb_selected_field_type = '';
        $this->fb_selected_field_label = '';
        $this->fb_selected_field_placeholder = '';
        $this->fb_selected_field_help_text = '';
        $this->fb_selected_field_required = false;
        $this->fb_selected_field_layout = 'full';
        $this->fb_selected_field_display_mode = 'list';
        $this->fb_selected_field_options_list = [];
    }

    public function addOption(): void
    {
        $this->fb_selected_field_options_list[] = '';
    }

    public function removeOption(int $index): void
    {
        if ($index >= 0 && $index < count($this->fb_selected_field_options_list)) {
            array_splice($this->fb_selected_field_options_list, $index, 1);
        }
    }

    public function selectForm(int $formId): void
    {
        $this->fb_selected_form_id = $formId;
        $this->loadSelectedForm();
    }

    public function selectField(int $fieldId): void
    {
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);
        $field = FormField::query()
            ->whereKey($fieldId)
            ->whereHas('form', fn($q) => $q->forOrg($orgId)->whereKey((int) $this->fb_selected_form_id))
            ->firstOrFail();
        $this->fb_selected_field_id = (int) $field->id;
        $this->fb_selected_field_key = (string) $field->key;
        $this->fb_selected_field_type = (string) $field->type;
        $this->fb_selected_field_label = (string) $field->label;
        $this->fb_selected_field_placeholder = (string) ($field->placeholder ?? '');
        $this->fb_selected_field_help_text = (string) ($field->help_text ?? '');
        $this->fb_selected_field_required = (bool) $field->required;
        $this->fb_selected_field_layout = (string) ($field->layout ?? 'full');
        $this->fb_selected_field_display_mode = (string) ($field->display_mode ?? 'list');
        $this->fb_selected_field_options_list = is_array($field->options) ? array_values($field->options) : [];
        $this->dispatch('field-selected');
    }

    public function saveSelectedForm(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);
        $validated = $this->validate([
            'fb_selected_form_name' => ['required', 'string', 'max:120'],
            'fb_selected_form_category_id' => ['nullable', 'integer'],
            'fb_selected_form_target_user_id' => ['nullable', 'integer'],
            'fb_selected_form_public' => ['boolean'],
            'fb_selected_form_creates_ticket' => ['boolean'],
            'fb_selected_form_slug' => ['nullable', 'string', 'max:140'],
            'fb_selected_form_public_title' => ['nullable', 'string', 'max:160'],
            'fb_selected_form_public_description' => ['nullable', 'string', 'max:2000'],
            'fb_selected_form_public_thank_you' => ['nullable', 'string', 'max:2000'],
            'fb_selected_form_description' => ['nullable', 'string', 'max:2000'],
        ]);
        if (($validated['fb_selected_form_category_id'] ?? null) !== null) {
            if (! TicketCategory::query()->where('organization_id', $orgId)->whereKey((int) $validated['fb_selected_form_category_id'])->exists()) {
                $this->addError('fb_selected_form_category_id', 'Catégorie invalide.');
                return;
            }
        }
        if (($validated['fb_selected_form_target_user_id'] ?? null) !== null) {
            if (! OrganizationMembership::query()->where('organization_id', $orgId)->where('user_id', (int) $validated['fb_selected_form_target_user_id'])->exists()) {
                $this->addError('fb_selected_form_target_user_id', 'Utilisateur invalide.');
                return;
            }
        }

        $isPublic = (bool) ($validated['fb_selected_form_public'] ?? false);
        $slug = trim((string) ($validated['fb_selected_form_slug'] ?? ''));
        if ($isPublic) {
            if ($slug === '') {
                $this->addError('fb_selected_form_slug', 'Le slug est requis pour publier.');
                return;
            }
            $slug = Str::slug($slug);
            if ($slug === '') {
                $this->addError('fb_selected_form_slug', 'Slug invalide.');
                return;
            }
            $exists = Form::query()
                ->whereNotNull('slug')
                ->where('slug', $slug)
                ->where('id', '!=', (int) $this->fb_selected_form_id)
                ->exists();
            if ($exists) {
                $this->addError('fb_selected_form_slug', 'Ce slug est déjà utilisé.');
                return;
            }
        } else {
            $slug = '';
        }

        Form::query()
            ->forOrg($orgId)
            ->whereKey((int) $this->fb_selected_form_id)
            ->update([
                'name' => $validated['fb_selected_form_name'],
                'description' => trim((string) ($validated['fb_selected_form_description'] ?? '')) ?: null,
                'ticket_category_id' => $validated['fb_selected_form_category_id'] ?? null,
                'target_user_id' => $validated['fb_selected_form_target_user_id'] ?? null,
                'is_public' => $isPublic,
                'creates_ticket' => (bool) ($validated['fb_selected_form_creates_ticket'] ?? true),
                'slug' => $slug !== '' ? $slug : null,
                'public_title' => trim((string) ($validated['fb_selected_form_public_title'] ?? '')) ?: null,
                'public_description' => trim((string) ($validated['fb_selected_form_public_description'] ?? '')) ?: null,
                'public_thank_you' => trim((string) ($validated['fb_selected_form_public_thank_you'] ?? '')) ?: null,
            ]);
        CacheHelper::invalidateForms($orgId);
        $this->dispatch('toast', type: 'success', message: 'Formulaire enregistré.');
    }

    public function publishForm(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);

        $form = Form::query()->forOrg($orgId)->whereKey((int) $this->fb_selected_form_id)->firstOrFail();

        if ($form->status === FormStatus::Published) {
            // Re-publish: increment version
            $form->incrementVersion();
        }

        $form->update(['status' => FormStatus::Published]);
        $this->fb_selected_form_status = FormStatus::Published->value;
        CacheHelper::invalidateForms($orgId);
        $this->dispatch('toast', type: 'success', message: 'Formulaire publié.');
    }

    public function unpublishForm(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);

        Form::query()->forOrg($orgId)->whereKey((int) $this->fb_selected_form_id)->update(['status' => FormStatus::Draft]);
        $this->fb_selected_form_status = FormStatus::Draft->value;
        CacheHelper::invalidateForms($orgId);
        $this->dispatch('toast', type: 'success', message: 'Formulaire repassé en brouillon.');
    }

    public function archiveForm(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);

        Form::query()->forOrg($orgId)->whereKey((int) $this->fb_selected_form_id)->update(['status' => FormStatus::Archived]);
        $this->fb_selected_form_status = FormStatus::Archived->value;
        CacheHelper::invalidateForms($orgId);
        $this->dispatch('toast', type: 'success', message: 'Formulaire archivé.');
    }

    public function generatePublicSlug(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);

        $base = $this->fb_selected_form_name ?: 'formulaire';
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = 'formulaire';
        }
        $slug = Str::limit($slug, 120, '');
        $candidate = $slug;
        $i = 2;
        while (Form::query()->where('slug', $candidate)->where('id', '!=', (int) $this->fb_selected_form_id)->exists()) {
            $candidate = Str::limit($slug . '-' . $i, 140, '');
            $i++;
        }

        $this->fb_selected_form_slug = $candidate;
        $this->fb_selected_form_public = true;

        // Enregistrer immédiatement en base pour que le lien public fonctionne tout de suite
        Form::query()
            ->forOrg($orgId)
            ->whereKey((int) $this->fb_selected_form_id)
            ->update([
                'is_public' => true,
                'slug' => $candidate,
            ]);
        CacheHelper::invalidateForms($orgId);
        $this->dispatch('toast', type: 'success', message: __('forms_builder.public_link_generated'));
    }

    public function saveSelectedField(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_field_id || ! $this->fb_selected_form_id, 404);
        $validated = $this->validate([
            'fb_selected_field_key' => ['required', 'string', 'max:64', 'regex:/^[a-z][a-z0-9_]*$/i'],
            'fb_selected_field_label' => ['required', 'string', 'max:120'],
            'fb_selected_field_placeholder' => ['nullable', 'string', 'max:255'],
            'fb_selected_field_help_text' => ['nullable', 'string', 'max:2000'],
            'fb_selected_field_required' => ['boolean'],
            'fb_selected_field_layout' => ['string', 'in:full,half,third'],
            'fb_selected_field_display_mode' => ['string', 'in:list,inline,grid,card'],
            'fb_selected_field_options_list' => ['nullable', 'array', 'max:100'],
            'fb_selected_field_options_list.*' => ['nullable', 'string', 'max:255'],
        ]);
        $field = FormField::query()
            ->whereKey((int) $this->fb_selected_field_id)
            ->whereHas('form', fn($q) => $q->forOrg($orgId)->whereKey((int) $this->fb_selected_form_id))
            ->firstOrFail();

        $key = Str::lower(trim((string) $validated['fb_selected_field_key']));
        $key = Str::limit($key, 64, '');
        if ($key === '') {
            $this->addError('fb_selected_field_key', 'Clé invalide.');
            return;
        }
        $exists = FormField::query()
            ->where('form_id', (int) $field->form_id)
            ->where('key', $key)
            ->where('id', '!=', (int) $field->id)
            ->exists();
        if ($exists) {
            $this->addError('fb_selected_field_key', 'Cette clé est déjà utilisée dans ce formulaire.');
            return;
        }

        // Build configuration
        $config = is_array($field->configuration) ? $field->configuration : [];
        $config['placeholder'] = trim((string) ($validated['fb_selected_field_placeholder'] ?? '')) ?: null;
        $config['help_text'] = trim((string) ($validated['fb_selected_field_help_text'] ?? '')) ?: null;
        $layout = $validated['fb_selected_field_layout'] ?? 'full';
        $config['layout'] = $layout !== 'full' ? $layout : null;
        $displayMode = $validated['fb_selected_field_display_mode'] ?? 'list';
        $config['display_mode'] = $displayMode !== 'list' ? $displayMode : null;

        if (in_array((string) $field->type, ['select', 'radio', 'checkbox'], true)) {
            $list = collect($validated['fb_selected_field_options_list'] ?? [])
                ->map(fn($v) => trim((string) $v))
                ->filter()
                ->values()
                ->all();
            $config['options'] = count($list) ? $list : null;
        }

        // Remove null values from config
        $config = array_filter($config, fn($v) => $v !== null);

        $field->update([
            'key' => $key,
            'label' => $validated['fb_selected_field_label'],
            'required' => (bool) ($validated['fb_selected_field_required'] ?? false),
            'configuration' => $config ?: (object) [],
        ]);
        $this->dispatch('toast', type: 'success', message: 'Champ enregistré.');
    }

    public function quickAddField(string $type): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);
        $form = Form::query()->forOrg($orgId)->whereKey((int) $this->fb_selected_form_id)->firstOrFail();
        $allowedTypes = ['text', 'textarea', 'select', 'checkbox', 'date', 'number', 'email', 'radio', 'datetime', 'file', 'section'];
        if (! in_array($type, $allowedTypes, true)) {
            return;
        }
        $defaultLabel = match ($type) {
            'textarea' => 'Paragraphe',
            'select' => 'Sélection',
            'radio' => 'Choix unique',
            'checkbox' => 'Case à cocher',
            'date' => 'Date',
            'datetime' => 'Date et heure',
            'number' => 'Nombre',
            'email' => 'Email',
            'file' => 'Fichier',
            'section' => 'Section',
            default => 'Texte court',
        };
        $key = Str::slug($defaultLabel, '_') ?: ('field_' . Str::lower(Str::random(6)));
        $key = Str::limit($key, 64, '');
        $baseKey = $key;
        $suffix = 2;
        while (FormField::query()->where('form_id', $form->id)->where('key', $key)->exists()) {
            $key = Str::limit($baseKey . '_' . $suffix, 64, '');
            $suffix++;
        }
        $config = [];
        if (in_array($type, ['select', 'radio', 'checkbox'], true)) {
            $config['options'] = ['Option 1', 'Option 2'];
        }
        $sortOrder = (int) (FormField::query()->where('form_id', (int) $form->id)->max('sort_order') ?? 0) + 10;
        $field = FormField::query()->create([
            'form_id' => $form->id,
            'key' => $key,
            'label' => $defaultLabel,
            'type' => $type,
            'required' => false,
            'configuration' => $config ?: (object) [],
            'sort_order' => $sortOrder,
            'form_version' => $form->current_version,
        ]);
        $this->selectField((int) $field->id);
    }

    public function createForm(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $validated = $this->validate([
            'fb_form_name' => ['required', 'string', 'max:120'],
        ]);
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);
        $form = Form::query()->create([
            'organization_id' => $orgId,
            'name' => $validated['fb_form_name'],
            'status' => FormStatus::Draft,
        ]);
        $this->fb_selected_form_id = (int) $form->id;
        $this->loadSelectedForm();
        $this->fb_form_name = '';
        CacheHelper::invalidateForms($orgId);
        $this->dispatch('toast', type: 'success', message: 'Formulaire créé.');
    }

    public function duplicateSelectedForm(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);

        $src = Form::query()
            ->forOrg($orgId)
            ->with(['fields'])
            ->whereKey((int) $this->fb_selected_form_id)
            ->firstOrFail();

        DB::transaction(function () use ($src, $orgId) {
            $new = Form::query()->create([
                'organization_id' => $orgId,
                'ticket_category_id' => $src->ticket_category_id,
                'name' => (string) $src->name . ' (Copie)',
                'description' => $src->description,
                'target_user_id' => $src->target_user_id,
                'status' => FormStatus::Draft,
                'is_public' => false,
                'slug' => null,
            ]);

            foreach ($src->fields as $field) {
                FormField::query()->create([
                    'form_id' => (int) $new->id,
                    'key' => (string) $field->key,
                    'label' => (string) $field->label,
                    'type' => (string) $field->type,
                    'required' => (bool) $field->required,
                    'configuration' => $field->configuration ?? (object) [],
                    'sort_order' => (int) $field->sort_order,
                    'form_version' => 1,
                ]);
            }

            $this->fb_selected_form_id = (int) $new->id;
        });

        $this->loadSelectedForm();
        CacheHelper::invalidateForms($orgId);
        $this->dispatch('toast', type: 'success', message: 'Formulaire dupliqué.');
    }

    public function deleteForm(int $formId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);
        Form::query()->forOrg($orgId)->whereKey($formId)->delete();
        if ($this->fb_selected_form_id === $formId) {
            $this->fb_selected_form_id = Form::query()->forOrg($orgId)->orderBy('name')->value('id');
            $this->loadSelectedForm();
        }
        CacheHelper::invalidateForms($orgId);
        $this->dispatch('toast', type: 'success', message: 'Formulaire supprimé.');
    }

    public function deleteFormField(int $fieldId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);
        $field = FormField::query()
            ->whereKey($fieldId)
            ->whereHas('form', fn($q) => $q->forOrg($orgId))
            ->firstOrFail();
        $field->delete();
        if ($this->fb_selected_field_id === $fieldId) {
            $this->fb_selected_field_id = null;
            $this->resetSelectedField();
        }
        $this->dispatch('toast', type: 'success', message: 'Champ supprimé.');
    }

    /** Move a field after another (afterFieldId = 0 means move to start). */
    public function reorderFormField(int $movedFieldId, int $afterFieldId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);

        $form = Form::query()->forOrg($orgId)->whereKey((int) $this->fb_selected_form_id)->firstOrFail();
        $fields = $form->fields()->orderBy('sort_order')->orderBy('id')->get();
        $ids = $fields->pluck('id')->values()->all();

        if (! in_array($movedFieldId, $ids, true)) {
            return;
        }

        $ids = array_values(array_filter($ids, fn($id) => $id !== $movedFieldId));
        if ($afterFieldId === 0) {
            $index = 0;
        } else {
            $pos = array_search($afterFieldId, $ids, true);
            $index = $pos === false ? count($ids) : $pos + 1;
        }
        array_splice($ids, $index, 0, [$movedFieldId]);

        foreach ($ids as $i => $id) {
            FormField::query()->whereKey($id)->update(['sort_order' => ($i + 1) * 10]);
        }
        $this->dispatch('toast', type: 'success', message: __('forms_builder.field_reordered'));
    }

    // ─── Assignments ────────────────────────────────────────────────

    public function assignForm(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->fb_selected_form_id, 404);

        $validated = $this->validate([
            'assign_user_id' => ['nullable', 'integer'],
            'assign_function_id' => ['nullable', 'integer'],
            'assign_due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        if (! $validated['assign_user_id'] && ! $validated['assign_function_id']) {
            $this->addError('assign_user_id', 'Sélectionnez un utilisateur ou une fonction.');
            return;
        }

        if ($validated['assign_user_id']) {
            if (! OrganizationMembership::query()->where('organization_id', $orgId)->where('user_id', (int) $validated['assign_user_id'])->exists()) {
                $this->addError('assign_user_id', 'Utilisateur invalide.');
                return;
            }
        }

        if ($validated['assign_function_id']) {
            if (! OrganizationFunction::query()->where('organization_id', $orgId)->whereKey((int) $validated['assign_function_id'])->exists()) {
                $this->addError('assign_function_id', 'Fonction invalide.');
                return;
            }
        }

        $form = Form::query()->forOrg($orgId)->whereKey((int) $this->fb_selected_form_id)->firstOrFail();
        $user = Auth::user();

        $assignment = FormAssignment::query()->create([
            'form_id' => $form->id,
            'user_id' => $validated['assign_user_id'] ?: null,
            'organization_function_id' => $validated['assign_function_id'] ?: null,
            'assigned_by' => $user->id,
            'status' => FormAssignmentStatus::Pending,
            'due_date' => $validated['assign_due_date'] ?? null,
            'form_version' => $form->current_version,
        ]);

        // Send notification
        if ($validated['assign_user_id']) {
            $target = \App\Models\User::find((int) $validated['assign_user_id']);
            if ($target && (int) $target->id !== (int) $user->id) {
                $target->notify(new FormAssignmentNotification(
                    formId: $form->id,
                    formName: $form->name,
                    assignmentId: $assignment->id,
                    assignedById: $user->id,
                    assignedByName: $user->name,
                    dueDate: $validated['assign_due_date'],
                ));
                event(new UserNotificationReceived(userId: $target->id, notificationType: 'form_assignment'));
            }
        } elseif ($validated['assign_function_id']) {
            // Notify all members with this function
            $memberIds = OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->where('organization_function_id', (int) $validated['assign_function_id'])
                ->pluck('user_id');
            foreach ($memberIds as $memberId) {
                if ((int) $memberId === (int) $user->id) {
                    continue;
                }
                $member = \App\Models\User::find($memberId);
                if ($member) {
                    $member->notify(new FormAssignmentNotification(
                        formId: $form->id,
                        formName: $form->name,
                        assignmentId: $assignment->id,
                        assignedById: $user->id,
                        assignedByName: $user->name,
                        dueDate: $validated['assign_due_date'],
                    ));
                    event(new UserNotificationReceived(userId: (int) $memberId, notificationType: 'form_assignment'));
                }
            }
        }

        $this->assign_user_id = null;
        $this->assign_function_id = null;
        $this->assign_due_date = null;
        $this->dispatch('toast', type: 'success', message: 'Formulaire assigné.');
    }

    public function deleteAssignment(int $assignmentId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }
        $orgId = $this->orgId();
        FormAssignment::query()
            ->whereKey($assignmentId)
            ->whereHas('form', fn($q) => $q->forOrg($orgId))
            ->delete();
        $this->dispatch('toast', type: 'success', message: 'Assignation supprimée.');
    }

    public function render()
    {
        $orgId = $this->orgId();

        $categories = $orgId ? Cache::remember(CacheHelper::categoriesKey($orgId, false), CacheHelper::TTL, function () use ($orgId) {
            return TicketCategory::query()->where('organization_id', $orgId)->orderBy('name')->get(['id', 'name', 'is_active']);
        }) : collect();

        $members = $orgId ? Cache::remember(CacheHelper::membersKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            return OrganizationMembership::query()->where('organization_id', $orgId)->with(['user:id,name,email'])->orderBy('id')->get();
        }) : collect();

        $forms = $orgId ? Cache::remember(CacheHelper::formsListKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            return Form::query()
                ->forOrg($orgId)
                ->with(['category:id,name', 'targetUser:id,name,email'])
                ->orderBy('name')
                ->get(['id', 'name', 'description', 'status', 'ticket_category_id', 'target_user_id', 'slug', 'current_version']);
        }) : collect();

        $selectedForm = null;
        if ($orgId && $this->fb_selected_form_id) {
            $selectedForm = Form::query()
                ->forOrg($orgId)
                ->with(['fields' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
                ->whereKey((int) $this->fb_selected_form_id)
                ->first();
        }

        $assignments = collect();
        $organizationFunctions = collect();
        if ($orgId && $this->fb_selected_form_id) {
            $assignments = FormAssignment::query()
                ->where('form_id', (int) $this->fb_selected_form_id)
                ->with(['user:id,name,email', 'assignedBy:id,name', 'organizationFunction:id,name', 'response'])
                ->latest()
                ->get();
            $organizationFunctions = Cache::remember(CacheHelper::orgFunctionsKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                return OrganizationFunction::query()
                    ->where('organization_id', $orgId)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name']);
            });
        }

        return view('livewire.admin.form-builder', [
            'categories' => $categories,
            'members' => $members,
            'forms' => $forms,
            'selectedForm' => $selectedForm,
            'assignments' => $assignments,
            'organizationFunctions' => $organizationFunctions,
        ]);
    }
}
