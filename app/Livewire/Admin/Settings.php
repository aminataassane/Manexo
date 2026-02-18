<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\TicketFormField;
use App\Models\TicketFormTemplate;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.manexo-app')]
#[Title('Paramètres')]
class Settings extends Component
{
    use WithFileUploads;

    public bool $canManage = false;
    public bool $isOwner = false;
    public bool $canManageForms = false;

    public string $name = '';
    public string $slug = '';
    public ?string $primary_color = null;
    public bool $slugManuallyEdited = false;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $logo = null;

    public ?string $currentLogoUrl = null;

    public ?int $default_category_id = null;
    public ?int $default_priority_id = null;
    public ?int $auto_close_days = null;
    public bool $members_can_edit = true;
    public bool $members_can_delete = false;

    public string $dangerConfirmName = '';

    /** Message de succès après enregistrement (affiché sans redirection). */
    public string $successMessage = '';

    // --- Form Builder (MVP)
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

    /**
     * New fields inputs per template.
     * @var array<int, array{label?: string, type?: string, required?: bool, options?: string, sort_order?: int}>
     */
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

    private function orgOrFail(): Organization
    {
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $org = Organization::query()->find($orgId);
        abort_if(! $org, 404);

        return $org;
    }

    public function mount(): void
    {
        $org = $this->orgOrFail();

        $role = $this->currentRole();
        $this->canManage = in_array($role, [OrganizationRole::Owner->value, OrganizationRole::Admin->value], true);
        $this->isOwner = $role === OrganizationRole::Owner->value;
        // In local/dev, allow building forms even as Member (demo friendly)
        $this->canManageForms = $this->canManage || app()->environment('local');

        $this->name = (string) $org->name;
        $this->slug = (string) $org->slug;
        $this->primary_color = $this->normalizeHexColorForDisplay($org->primary_color);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $this->currentLogoUrl = $org->logo_path ? $disk->url($org->logo_path) : null;

        $settings = is_array($org->settings) ? $org->settings : [];

        $this->default_category_id = isset($settings['defaults']['ticket_category_id'])
            ? (int) $settings['defaults']['ticket_category_id']
            : null;
        $this->default_priority_id = isset($settings['defaults']['ticket_priority_id'])
            ? (int) $settings['defaults']['ticket_priority_id']
            : null;
        $this->auto_close_days = isset($settings['workflow']['auto_close_days'])
            ? (int) $settings['workflow']['auto_close_days']
            : null;
        $this->members_can_edit = (bool) ($settings['permissions']['members_can_edit'] ?? true);
        $this->members_can_delete = (bool) ($settings['permissions']['members_can_delete'] ?? false);

        $this->fb_selected_template_id = TicketFormTemplate::query()
            ->where('organization_id', $org->id)
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
        abort_if(! $orgId, 403);
        abort_if(! $this->fb_selected_template_id, 404);

        $field = TicketFormField::query()
            ->whereKey($fieldId)
            ->whereHas('template', fn($q) => $q->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id))
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
        abort_if(! $orgId, 403);
        abort_if(! $this->fb_selected_template_id, 404);

        $validated = $this->validate([
            'fb_selected_template_name' => ['required', 'string', 'max:120'],
            'fb_selected_template_category_id' => ['nullable', 'integer'],
            'fb_selected_template_target_user_id' => ['nullable', 'integer'],
            'fb_selected_template_active' => ['boolean'],
        ]);

        if (($validated['fb_selected_template_category_id'] ?? null) !== null) {
            $catOk = TicketCategory::query()
                ->where('organization_id', $orgId)
                ->whereKey((int) $validated['fb_selected_template_category_id'])
                ->exists();
            if (! $catOk) {
                $this->addError('fb_selected_template_category_id', 'Catégorie invalide.');
                return;
            }
        }

        if (($validated['fb_selected_template_target_user_id'] ?? null) !== null) {
            $userOk = OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->where('user_id', (int) $validated['fb_selected_template_target_user_id'])
                ->exists();
            if (! $userOk) {
                $this->addError('fb_selected_template_target_user_id', 'Utilisateur invalide pour cette entreprise.');
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

        $this->dispatch('toast', type: 'success', message: "Formulaire enregistré.");
    }

    public function saveSelectedField(): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);
        abort_if(! $this->fb_selected_field_id || ! $this->fb_selected_template_id, 404);

        $validated = $this->validate([
            'fb_selected_field_label' => ['required', 'string', 'max:120'],
            'fb_selected_field_required' => ['boolean'],
            'fb_selected_field_options' => ['nullable', 'string', 'max:2000'],
        ]);

        $field = TicketFormField::query()
            ->whereKey((int) $this->fb_selected_field_id)
            ->whereHas('template', fn($q) => $q->where('organization_id', $orgId)->whereKey((int) $this->fb_selected_template_id))
            ->firstOrFail();

        $options = $field->options;
        if ((string) $field->type === 'select') {
            $raw = trim((string) ($validated['fb_selected_field_options'] ?? ''));
            $list = collect(preg_split('/[\r\n,]+/', $raw))
                ->map(fn($v) => trim((string) $v))
                ->filter()
                ->values()
                ->all();
            $options = count($list) ? $list : null;
        }

        $field->update([
            'label' => $validated['fb_selected_field_label'],
            'required' => (bool) ($validated['fb_selected_field_required'] ?? false),
            'options' => $options,
        ]);

        $this->dispatch('toast', type: 'success', message: "Champ enregistré.");
    }

    public function quickAddField(string $type): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);
        abort_if(! $this->fb_selected_template_id, 404);

        $tpl = TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->fb_selected_template_id)
            ->firstOrFail();

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

        $options = null;
        if ($type === 'select') {
            $options = ['Option 1', 'Option 2'];
        }

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

    public function updatedName(string $value): void
    {
        if ($this->slugManuallyEdited) {
            return;
        }

        $this->slug = Str::slug($value);
    }

    public function updatedSlug(string $value): void
    {
        // Dès que l'utilisateur touche au slug, on n'auto-modifie plus.
        $this->slugManuallyEdited = true;
        $this->slug = Str::slug($value);
    }

    public function updatedLogo(): void
    {
        $this->validate([
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB
        ]);
    }

    /**
     * Normalise une couleur hex pour l'affichage au chargement (avec ou sans #, 3 ou 6 caractères) en #rrggbb.
     */
    private function normalizeHexColorForDisplay(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        return $this->normalizeHexColor(is_string($value) ? trim($value) : (string) $value) ?: null;
    }

    /**
     * Normalise une couleur hex (avec ou sans #, 3 ou 6 caractères) en #rrggbb.
     */
    private function normalizeHexColor(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        if (str_starts_with($value, '#')) {
            $value = substr($value, 1);
        }
        $value = strtolower($value);
        if (preg_match('/^[0-9a-f]{3}$/', $value)) {
            $value = $value[0] . $value[0] . $value[1] . $value[1] . $value[2] . $value[2];
        }
        if (! preg_match('/^[0-9a-f]{6}$/', $value)) {
            return null;
        }

        return '#' . $value;
    }

    public function save()
    {
        if (! $this->canManage) {
            $this->successMessage = '';
            $this->addError('canManage', __('Accès refusé: réservé aux admins.'));
            return;
        }

        $this->successMessage = '';
        if ($this->primary_color === '') {
            $this->primary_color = null;
        } else {
            $normalized = $this->normalizeHexColor($this->primary_color);
            if ($normalized === null && $this->primary_color !== null && trim($this->primary_color) !== '') {
                $this->addError('primary_color', __('La couleur doit être un code hex valide (ex: #000000 ou 000000).'));
                return;
            }
            $this->primary_color = $normalized;
        }

        $org = $this->orgOrFail();
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:organizations,slug,' . $org->id],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'default_category_id' => ['nullable', 'integer'],
            'default_priority_id' => ['nullable', 'integer'],
            'auto_close_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'members_can_edit' => ['boolean'],
            'members_can_delete' => ['boolean'],
        ]);

        if ($validated['default_category_id'] ?? null) {
            $catOk = TicketCategory::query()
                ->where('organization_id', $org->id)
                ->where('is_active', true)
                ->whereKey((int) $validated['default_category_id'])
                ->exists();

            if (! $catOk) {
                $this->addError('default_category_id', "Catégorie invalide.");
                return;
            }
        }

        if ($validated['default_priority_id'] ?? null) {
            $prioOk = TicketPriority::query()
                ->where('organization_id', $org->id)
                ->where('is_active', true)
                ->whereKey((int) $validated['default_priority_id'])
                ->exists();

            if (! $prioOk) {
                $this->addError('default_priority_id', "Priorité invalide.");
                return;
            }
        }

        $logoPath = $org->logo_path;
        if ($this->logo) {
            $ext = $this->logo->getClientOriginalExtension() ?: 'png';
            $filename = 'org-' . $org->id . '-' . Str::lower(Str::random(10)) . '.' . $ext;
            $logoPath = $this->logo->storeAs('org-logos', $filename, 'public');
        }

        $settings = is_array($org->settings) ? $org->settings : [];
        $settings['defaults'] = [
            'ticket_category_id' => $validated['default_category_id'] ?? null,
            'ticket_priority_id' => $validated['default_priority_id'] ?? null,
        ];
        $settings['workflow'] = [
            'auto_close_days' => $validated['auto_close_days'] ?? null,
        ];
        $settings['permissions'] = [
            'members_can_edit' => (bool) ($validated['members_can_edit'] ?? true),
            'members_can_delete' => (bool) ($validated['members_can_delete'] ?? false),
        ];

        $org->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'primary_color' => $validated['primary_color'] ?: null,
            'logo_path' => $logoPath,
            'settings' => $settings,
        ]);

        $org->refresh();

        $this->name = (string) $org->name;
        $this->slug = (string) $org->slug;
        $this->primary_color = $this->normalizeHexColorForDisplay($org->primary_color);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $this->currentLogoUrl = $org->logo_path ? $disk->url($org->logo_path) : null;
        $this->logo = null;

        $settings = is_array($org->settings) ? $org->settings : [];
        $this->default_category_id = isset($settings['defaults']['ticket_category_id']) ? (int) $settings['defaults']['ticket_category_id'] : null;
        $this->default_priority_id = isset($settings['defaults']['ticket_priority_id']) ? (int) $settings['defaults']['ticket_priority_id'] : null;
        $this->auto_close_days = isset($settings['workflow']['auto_close_days']) ? (int) $settings['workflow']['auto_close_days'] : null;
        $this->members_can_edit = (bool) ($settings['permissions']['members_can_edit'] ?? true);
        $this->members_can_delete = (bool) ($settings['permissions']['members_can_delete'] ?? false);

        view()->share('currentOrganization', $org);

        $this->successMessage = __('Paramètres mis à jour.');
    }

    public function removeLogo(): void
    {
        if (! $this->canManage) {
            session()->flash('settings_status', "Accès refusé: réservé aux admins.");
            return;
        }

        $org = $this->orgOrFail();

        if ($org->logo_path) {
            Storage::disk('public')->delete($org->logo_path);
        }

        $org->update(['logo_path' => null]);
        $this->currentLogoUrl = null;
        $this->logo = null;

        session()->flash('settings_status', "Logo supprimé.");
    }

    public function deleteOrganization(): void
    {
        if (! $this->isOwner) {
            session()->flash('settings_status', "Accès refusé: réservé au propriétaire.");
            return;
        }

        $org = $this->orgOrFail();

        if (trim($this->dangerConfirmName) !== (string) $org->name) {
            $this->addError('dangerConfirmName', "Le nom ne correspond pas.");
            return;
        }

        $org->delete();
        session()->forget('current_organization_id');

        $this->redirectRoute('organizations.select');
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
            $catOk = TicketCategory::query()
                ->where('organization_id', $orgId)
                ->whereKey((int) $validated['fb_template_category_id'])
                ->exists();
            if (! $catOk) {
                $this->addError('fb_template_category_id', 'Catégorie invalide.');
                return;
            }
        }

        if (($validated['fb_template_target_user_id'] ?? null) !== null) {
            $userOk = OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->where('user_id', (int) $validated['fb_template_target_user_id'])
                ->exists();
            if (! $userOk) {
                $this->addError('fb_template_target_user_id', 'Utilisateur invalide pour cette entreprise.');
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

        $this->dispatch('toast', type: 'success', message: "Formulaire créé.");
    }

    public function toggleFormTemplate(int $templateId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $tpl = TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->whereKey($templateId)
            ->firstOrFail();

        $tpl->update(['is_active' => ! (bool) $tpl->is_active]);
        $this->dispatch('toast', type: 'success', message: "Statut mis à jour.");
    }

    public function deleteFormTemplate(int $templateId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->whereKey($templateId)
            ->delete();

        unset($this->newFields[$templateId]);
        $this->dispatch('toast', type: 'success', message: "Formulaire supprimé.");
    }

    public function addFormField(int $templateId): void
    {
        if (! $this->canManageForms) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $tpl = TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->whereKey($templateId)
            ->firstOrFail();

        $data = $this->newFields[$templateId] ?? [];
        $label = trim((string) ($data['label'] ?? ''));
        $type = (string) ($data['type'] ?? 'text');
        $required = (bool) ($data['required'] ?? false);
        $optionsRaw = trim((string) ($data['options'] ?? ''));
        $sortOrder = (int) ($data['sort_order'] ?? 0);

        $allowedTypes = ['text', 'textarea', 'select', 'checkbox', 'date', 'number', 'email'];
        if ($label === '') {
            $this->addError("newFields.$templateId.label", "Label requis.");
            return;
        }
        if (! in_array($type, $allowedTypes, true)) {
            $this->addError("newFields.$templateId.type", "Type invalide.");
            return;
        }

        $key = Str::slug($label, '_');
        $key = $key !== '' ? $key : ('field_' . Str::lower(Str::random(6)));
        $key = Str::limit($key, 64, '');

        $options = null;
        if ($type === 'select') {
            $list = collect(preg_split('/[\r\n,]+/', $optionsRaw))
                ->map(fn($v) => trim((string) $v))
                ->filter()
                ->values()
                ->all();

            if (! count($list)) {
                $this->addError("newFields.$templateId.options", "Options requises (séparées par virgule).");
                return;
            }

            $options = $list;
        }

        // Ensure unique key per template (auto-suffix)
        $baseKey = $key;
        $suffix = 2;
        while (TicketFormField::query()->where('template_id', $tpl->id)->where('key', $key)->exists()) {
            $key = Str::limit($baseKey . '_' . $suffix, 64, '');
            $suffix++;
        }

        TicketFormField::query()->create([
            'template_id' => $tpl->id,
            'key' => $key,
            'label' => $label,
            'type' => $type,
            'required' => $required,
            'options' => $options,
            'sort_order' => $sortOrder,
        ]);

        $this->newFields[$templateId] = ['label' => '', 'type' => 'text', 'required' => false, 'options' => '', 'sort_order' => 0];
        $this->dispatch('toast', type: 'success', message: "Champ ajouté.");
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
        $this->dispatch('toast', type: 'success', message: "Champ supprimé.");
    }

    public function render()
    {
        $orgId = $this->orgId();

        $org = $orgId ? Organization::query()->find($orgId) : null;

        $categories = $orgId
            ? TicketCategory::query()
            ->where('organization_id', $orgId)
            ->orderBy('name')
            ->get(['id', 'name', 'is_active'])
            : collect();

        $priorities = $orgId
            ? TicketPriority::query()
            ->where('organization_id', $orgId)
            ->orderByDesc('level')
            ->get(['id', 'name', 'level', 'is_active'])
            : collect();

        $members = $orgId
            ? OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->with(['user:id,name,email'])
            ->orderBy('id')
            ->get()
            : collect();

        $formTemplates = $orgId
            ? TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->with([
                'category:id,name',
                'targetUser:id,name,email',
                'fields:id,template_id,key,label,type,required,options,sort_order',
            ])
            ->orderBy('name')
            ->get()
            : collect();

        return view('livewire.admin.settings', [
            'org' => $org,
            'categories' => $categories,
            'priorities' => $priorities,
            'members' => $members,
            'formTemplates' => $formTemplates,
        ]);
    }
}

