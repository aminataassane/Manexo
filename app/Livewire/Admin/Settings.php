<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Helpers\CacheHelper;
use App\Services\OrganizationAuditService;
use App\Models\Organization;
use App\Models\OrganizationFunction;
use App\Models\OrganizationMembership;
use App\Models\OrganizationRolePermission;
use App\Models\RoleDefinition;
use App\Models\Form;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

    // --- Category CRUD
    public string $newCategoryName = '';
    public ?int $editingCategoryId = null;
    public string $editingCategoryName = '';

    // --- Priority CRUD
    public string $newPriorityName = '';
    public ?int $newPriorityLevel = null;
    public ?int $editingPriorityId = null;
    public string $editingPriorityName = '';
    public ?int $editingPriorityLevel = null;

    // --- Function (fonction métier) CRUD
    public string $newFunctionName = '';
    public ?int $editingFunctionId = null;
    public string $editingFunctionName = '';

    public string $dangerConfirmName = '';

    /** Mode de résolution des tickets (flexible | strict) */
    public string $ticket_resolution_mode = 'flexible';

    /** Paramètres formulaires (onglet Formulaires) */
    public ?int $forms_default_due_days = 7;
    public bool $forms_notify_on_response = true;
    public ?int $forms_default_expiry_days = 30;

    /** Message de succès après enregistrement (affiché sans redirection). */
    public string $successMessage = '';

    // --- Roles & Permissions
    /** @var array<string, array<string, bool>> [role => [permission => bool]] */
    public array $rolePermissions = [];
    public string $selectedRole = 'admin';

    // --- Role CRUD
    public string $newRoleName = '';
    public string $newRoleBaseSlug = '';
    public ?int $editingRoleId = null;
    public string $editingRoleName = '';

    private function orgId(): int
    {
        return (int) session('current_organization_id');
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

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->canManage = $user->hasAnyPermission([
            Permission::SettingsManageBranding,
            Permission::SettingsManageCategories,
            Permission::SettingsManagePriorities,
            Permission::SettingsManageFunctions,
            Permission::SettingsManageForms,
            Permission::SettingsManageRoles,
        ]);
        $this->isOwner = $user->hasPermission(Permission::SettingsDeleteOrg);

        $this->name = (string) $org->name;
        $this->slug = (string) $org->slug;
        $this->primary_color = $this->normalizeHexColorForDisplay($org->primary_color);
        $this->currentLogoUrl = $org->logo_path ? asset('storage/' . ltrim($org->logo_path, '/')) : null;

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
        $this->ticket_resolution_mode = $settings['workflow']['ticket_resolution_mode'] ?? 'flexible';

        $forms = $settings['forms'] ?? [];
        $this->forms_default_due_days = isset($forms['default_due_days']) ? (int) $forms['default_due_days'] : 7;
        $this->forms_notify_on_response = (bool) ($forms['notify_on_response'] ?? true);
        $this->forms_default_expiry_days = isset($forms['default_expiry_days']) ? (int) $forms['default_expiry_days'] : 30;

        $this->loadRolePermissions();
    }

    public function loadRolePermissions(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        $existing = OrganizationRolePermission::query()
            ->where('organization_id', $orgId)
            ->get()
            ->groupBy('role')
            ->map(fn ($rows) => $rows->pluck('permission')->flip()->map(fn () => true)->all())
            ->all();

        $allPermissions = array_map(fn (Permission $p) => $p->value, Permission::cases());

        $roleSlugs = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->pluck('slug');

        $this->rolePermissions = [];
        foreach ($roleSlugs as $slug) {
            $granted = $existing[$slug] ?? [];
            $this->rolePermissions[$slug] = [];
            foreach ($allPermissions as $perm) {
                $this->rolePermissions[$slug][$perm] = isset($granted[$perm]);
            }
        }

        // Ensure selectedRole is valid
        if (! isset($this->rolePermissions[$this->selectedRole]) && $roleSlugs->isNotEmpty()) {
            $this->selectedRole = $roleSlugs->contains('admin') ? 'admin' : $roleSlugs->first();
        }
    }

    public function selectRoleTab(string $role): void
    {
        $orgId = $this->orgId();
        $allowed = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->pluck('slug')
            ->all();

        if (in_array($role, $allowed, true)) {
            $this->selectedRole = $role;
        }
    }

    /** Bascule une permission pour un rôle (évite wire:model avec clés contenant un point). */
    public function toggleRolePermission(string $role, string $permission): void
    {
        if ($role === 'owner') {
            return;
        }
        $allValid = array_map(fn (Permission $p) => $p->value, Permission::cases());
        if (! in_array($permission, $allValid, true)) {
            return;
        }
        if (! isset($this->rolePermissions[$role])) {
            $this->rolePermissions[$role] = [];
        }
        $current = $this->rolePermissions[$role][$permission] ?? false;
        $this->rolePermissions[$role][$permission] = ! $current;
    }

    public function saveRolePermissions(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageRoles)) {
            $this->addError('rolePermissions', __('Acces refuse.'));
            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        $role = $this->selectedRole;

        // Owner permissions cannot be changed
        if ($role === 'owner') {
            return;
        }

        $allValid = array_map(fn (Permission $p) => $p->value, Permission::cases());
        $granted = [];
        foreach ($this->rolePermissions[$role] ?? [] as $perm => $enabled) {
            if ($enabled && in_array($perm, $allValid, true)) {
                $granted[] = $perm;
            }
        }

        // Delete all existing permissions for this org+role, then insert new ones
        OrganizationRolePermission::query()
            ->where('organization_id', $orgId)
            ->where('role', $role)
            ->delete();

        $now = now();
        $rows = array_map(fn (string $p) => [
            'organization_id' => $orgId,
            'role' => $role,
            'permission' => $p,
            'created_at' => $now,
            'updated_at' => $now,
        ], $granted);

        if (! empty($rows)) {
            OrganizationRolePermission::query()->insert($rows);
        }

        CacheHelper::invalidateRolePermissions($orgId);

        OrganizationAuditService::log(
            'settings.role_permissions_updated',
            'role',
            null,
            ['role' => $role, 'permissions_count' => count($granted)],
        );

        $this->dispatch('toast', type: 'success', message: __('settings.permissions_saved'));
    }

    /** Enregistre les permissions de tous les rôles (sauf owner) en une fois. */
    public function saveAllRolePermissions(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageRoles)) {
            $this->addError('rolePermissions', __('Acces refuse.'));
            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        $allValid = array_map(fn (Permission $p) => $p->value, Permission::cases());

        foreach ($this->rolePermissions as $role => $perms) {
            if ($role === 'owner') {
                continue;
            }
            $granted = [];
            foreach ($perms ?? [] as $perm => $enabled) {
                if ($enabled && in_array($perm, $allValid, true)) {
                    $granted[] = $perm;
                }
            }
            OrganizationRolePermission::query()
                ->where('organization_id', $orgId)
                ->where('role', $role)
                ->delete();
            $now = now();
            $rows = array_map(fn (string $p) => [
                'organization_id' => $orgId,
                'role' => $role,
                'permission' => $p,
                'created_at' => $now,
                'updated_at' => $now,
            ], $granted);
            if (! empty($rows)) {
                OrganizationRolePermission::query()->insert($rows);
            }
        }

        CacheHelper::invalidateRolePermissions($orgId);
        $this->dispatch('toast', type: 'success', message: __('settings.permissions_saved'));
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

        // Enregistrer le logo immédiatement : Livewire ne renvoie pas le fichier lors du submit "Save"
        if (! $this->logo || ! $this->canManage) {
            return;
        }

        $org = $this->orgOrFail();
        $ext = $this->logo->getClientOriginalExtension() ?: 'png';
        $filename = 'org-' . $org->id . '-' . Str::lower(Str::random(10)) . '.' . $ext;
        $logoPath = $this->logo->storeAs('org-logos', $filename, 'public');

        if ($org->logo_path) {
            Storage::disk('public')->delete($org->logo_path);
        }

        $org->update(['logo_path' => $logoPath]);
        $org->refresh();

        $this->currentLogoUrl = asset('storage/' . ltrim($logoPath, '/'));
        $this->logo = null;

        CacheHelper::invalidateAll($this->orgId());
        $this->dispatch('toast', type: 'success', message: __('Logo enregistré.'));
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
            'ticket_resolution_mode' => ['required', 'in:flexible,strict'],
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
            'ticket_resolution_mode' => $this->ticket_resolution_mode,
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

        $this->currentLogoUrl = $org->logo_path ? asset('storage/' . ltrim($org->logo_path, '/')) : null;
        $this->logo = null;

        $settings = is_array($org->settings) ? $org->settings : [];
        $this->default_category_id = isset($settings['defaults']['ticket_category_id']) ? (int) $settings['defaults']['ticket_category_id'] : null;
        $this->default_priority_id = isset($settings['defaults']['ticket_priority_id']) ? (int) $settings['defaults']['ticket_priority_id'] : null;
        $this->auto_close_days = isset($settings['workflow']['auto_close_days']) ? (int) $settings['workflow']['auto_close_days'] : null;
        $this->ticket_resolution_mode = $settings['workflow']['ticket_resolution_mode'] ?? 'flexible';
        $this->members_can_edit = (bool) ($settings['permissions']['members_can_edit'] ?? true);
        $this->members_can_delete = (bool) ($settings['permissions']['members_can_delete'] ?? false);

        view()->share('currentOrganization', $org);

        CacheHelper::invalidateAll($this->orgId());

        OrganizationAuditService::log(
            'settings.updated',
            'organization',
            (int) $org->id,
            ['fields' => array_keys($validated)],
        );

        $this->successMessage = __('Paramètres mis à jour.');
    }

    public function saveFormsSettings(): void
    {
        if (! $this->canManage) {
            $this->addError('canManage', __('Accès refusé.'));
            return;
        }

        $validated = $this->validate([
            'forms_default_due_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'forms_notify_on_response' => ['boolean'],
            'forms_default_expiry_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $org = $this->orgOrFail();
        $settings = is_array($org->settings) ? $org->settings : [];
        $settings['forms'] = [
            'default_due_days' => $validated['forms_default_due_days'] ?? null,
            'notify_on_response' => (bool) ($validated['forms_notify_on_response'] ?? true),
            'default_expiry_days' => $validated['forms_default_expiry_days'] ?? null,
        ];
        $org->update(['settings' => $settings]);

        $this->forms_default_due_days = $validated['forms_default_due_days'] ?? null;
        $this->forms_notify_on_response = (bool) ($validated['forms_notify_on_response'] ?? true);
        $this->forms_default_expiry_days = $validated['forms_default_expiry_days'] ?? null;

        CacheHelper::invalidateAll($this->orgId());
        $this->dispatch('toast', type: 'success', message: __('settings.forms_settings_saved'));
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

        OrganizationAuditService::log(
            'organization.deleted',
            'organization',
            (int) $org->id,
            ['name' => $org->name],
        );

        $org->delete();
        session()->forget('current_organization_id');

        $this->redirectRoute('organizations.select');
    }

    // ─── Category CRUD ────────────────────────────────────────────────

    public function createCategory(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'newCategoryName' => ['required', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $slug = Str::slug($this->newCategoryName);
        if ($slug === '') {
            $slug = 'cat-' . Str::lower(Str::random(6));
        }

        $baseSlug = $slug;
        $suffix = 2;
        while (TicketCategory::query()->where('organization_id', $orgId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        TicketCategory::query()->create([
            'organization_id' => $orgId,
            'name' => trim($this->newCategoryName),
            'slug' => $slug,
            'is_active' => true,
        ]);

        $this->newCategoryName = '';
        CacheHelper::invalidateCategories($orgId);
        $this->dispatch('toast', type: 'success', message: 'Catégorie créée.');
    }

    public function startEditCategory(int $id): void
    {
        $orgId = $this->orgId();
        $cat = TicketCategory::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $this->editingCategoryId = (int) $cat->id;
        $this->editingCategoryName = (string) $cat->name;
    }

    public function cancelEditCategory(): void
    {
        $this->editingCategoryId = null;
        $this->editingCategoryName = '';
    }

    public function updateCategory(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'editingCategoryName' => ['required', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->editingCategoryId, 403);

        $cat = TicketCategory::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->editingCategoryId)
            ->firstOrFail();

        $slug = Str::slug($this->editingCategoryName);
        if ($slug === '') {
            $slug = 'cat-' . Str::lower(Str::random(6));
        }

        $baseSlug = $slug;
        $suffix = 2;
        while (
            TicketCategory::query()
                ->where('organization_id', $orgId)
                ->where('slug', $slug)
                ->where('id', '!=', $cat->id)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        $cat->update([
            'name' => trim($this->editingCategoryName),
            'slug' => $slug,
        ]);

        $this->editingCategoryId = null;
        $this->editingCategoryName = '';
        CacheHelper::invalidateCategories($orgId);
        $this->dispatch('toast', type: 'success', message: 'Catégorie mise à jour.');
    }

    public function toggleCategory(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $cat = TicketCategory::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $cat->update(['is_active' => ! $cat->is_active]);
        CacheHelper::invalidateCategories($orgId);
        $this->dispatch('toast', type: 'success', message: $cat->is_active ? 'Catégorie activée.' : 'Catégorie désactivée.');
    }

    public function deleteCategory(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $cat = TicketCategory::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        if ($cat->tickets()->exists()) {
            $this->dispatch('toast', type: 'error', message: 'Impossible de supprimer : des tickets utilisent cette catégorie.');
            return;
        }

        $deletedId = (int) $cat->id;
        $cat->delete();

        if ($this->default_category_id === $deletedId) {
            $this->default_category_id = null;
            $org = $this->orgOrFail();
            $settings = is_array($org->settings) ? $org->settings : [];
            $settings['defaults']['ticket_category_id'] = null;
            $org->update(['settings' => $settings]);
        }

        CacheHelper::invalidateCategories($orgId);
        $this->dispatch('toast', type: 'success', message: 'Catégorie supprimée.');
    }

    // ─── Priority CRUD ──────────────────────────────────────────────

    public function createPriority(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'newPriorityName' => ['required', 'string', 'max:80'],
            'newPriorityLevel' => ['required', 'integer', 'min:0'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        if (TicketPriority::query()->where('organization_id', $orgId)->where('level', (int) $this->newPriorityLevel)->exists()) {
            $this->addError('newPriorityLevel', 'Ce niveau est déjà utilisé.');
            return;
        }

        TicketPriority::query()->create([
            'organization_id' => $orgId,
            'name' => trim($this->newPriorityName),
            'level' => (int) $this->newPriorityLevel,
            'is_active' => true,
        ]);

        $this->newPriorityName = '';
        $this->newPriorityLevel = null;
        CacheHelper::invalidatePriorities($orgId);
        $this->dispatch('toast', type: 'success', message: 'Priorité créée.');
    }

    public function startEditPriority(int $id): void
    {
        $orgId = $this->orgId();
        $prio = TicketPriority::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $this->editingPriorityId = (int) $prio->id;
        $this->editingPriorityName = (string) $prio->name;
        $this->editingPriorityLevel = (int) $prio->level;
    }

    public function cancelEditPriority(): void
    {
        $this->editingPriorityId = null;
        $this->editingPriorityName = '';
        $this->editingPriorityLevel = null;
    }

    public function updatePriority(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'editingPriorityName' => ['required', 'string', 'max:80'],
            'editingPriorityLevel' => ['required', 'integer', 'min:0'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->editingPriorityId, 403);

        $prio = TicketPriority::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->editingPriorityId)
            ->firstOrFail();

        if (
            TicketPriority::query()
                ->where('organization_id', $orgId)
                ->where('level', (int) $this->editingPriorityLevel)
                ->where('id', '!=', $prio->id)
                ->exists()
        ) {
            $this->addError('editingPriorityLevel', 'Ce niveau est déjà utilisé par une autre priorité.');
            return;
        }

        $prio->update([
            'name' => trim($this->editingPriorityName),
            'level' => (int) $this->editingPriorityLevel,
        ]);

        $this->editingPriorityId = null;
        $this->editingPriorityName = '';
        $this->editingPriorityLevel = null;
        CacheHelper::invalidatePriorities($orgId);
        $this->dispatch('toast', type: 'success', message: 'Priorité mise à jour.');
    }

    public function togglePriority(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $prio = TicketPriority::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $prio->update(['is_active' => ! $prio->is_active]);
        CacheHelper::invalidatePriorities($orgId);
        $this->dispatch('toast', type: 'success', message: $prio->is_active ? 'Priorité activée.' : 'Priorité désactivée.');
    }

    public function deletePriority(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $prio = TicketPriority::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        if ($prio->tickets()->exists()) {
            $this->dispatch('toast', type: 'error', message: 'Impossible de supprimer : des tickets utilisent cette priorité.');
            return;
        }

        $deletedId = (int) $prio->id;
        $prio->delete();

        if ($this->default_priority_id === $deletedId) {
            $this->default_priority_id = null;
            $org = $this->orgOrFail();
            $settings = is_array($org->settings) ? $org->settings : [];
            $settings['defaults']['ticket_priority_id'] = null;
            $org->update(['settings' => $settings]);
        }

        CacheHelper::invalidatePriorities($orgId);
        $this->dispatch('toast', type: 'success', message: 'Priorité supprimée.');
    }

    // ─── Role CRUD ────────────────────────────────────────────────

    public function createRole(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'newRoleName' => ['required', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $slug = Str::slug($this->newRoleName);
        if ($slug === '') {
            $slug = 'role-' . Str::lower(Str::random(6));
        }

        $baseSlug = $slug;
        $suffix = 2;
        while (RoleDefinition::query()->where('organization_id', $orgId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        RoleDefinition::query()->create([
            'organization_id' => $orgId,
            'name' => trim($this->newRoleName),
            'slug' => $slug,
            'is_default' => false,
        ]);

        // Clone permissions from base role if specified
        if ($this->newRoleBaseSlug !== '') {
            $basePerms = OrganizationRolePermission::query()
                ->where('organization_id', $orgId)
                ->where('role', $this->newRoleBaseSlug)
                ->pluck('permission');

            $now = now();
            $rows = $basePerms->map(fn (string $p) => [
                'organization_id' => $orgId,
                'role' => $slug,
                'permission' => $p,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if (! empty($rows)) {
                OrganizationRolePermission::query()->insert($rows);
            }
        }

        $this->newRoleName = '';
        $this->newRoleBaseSlug = '';
        $this->loadRolePermissions();
        CacheHelper::invalidateRolePermissions($orgId);
        $this->dispatch('toast', type: 'success', message: __('settings.role_created'));
    }

    public function startEditRole(int $id): void
    {
        $orgId = $this->orgId();
        $role = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $this->editingRoleId = (int) $role->id;
        $this->editingRoleName = (string) $role->name;
    }

    public function cancelEditRole(): void
    {
        $this->editingRoleId = null;
        $this->editingRoleName = '';
    }

    public function updateRoleName(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'editingRoleName' => ['required', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->editingRoleId, 403);

        $role = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->editingRoleId)
            ->firstOrFail();

        if ($role->is_default) {
            // Default roles: only rename display name, keep slug
            $role->update(['name' => trim($this->editingRoleName)]);
        } else {
            // Custom roles: rename name + slug + update memberships.role and permissions.role
            $newSlug = Str::slug($this->editingRoleName);
            if ($newSlug === '') {
                $newSlug = 'role-' . Str::lower(Str::random(6));
            }

            $baseSlug = $newSlug;
            $suffix = 2;
            while (
                RoleDefinition::query()
                    ->where('organization_id', $orgId)
                    ->where('slug', $newSlug)
                    ->where('id', '!=', $role->id)
                    ->exists()
            ) {
                $newSlug = $baseSlug . '-' . $suffix;
                $suffix++;
            }

            $oldSlug = $role->slug;

            DB::transaction(function () use ($role, $orgId, $oldSlug, $newSlug) {
                $role->update([
                    'name' => trim($this->editingRoleName),
                    'slug' => $newSlug,
                ]);

                OrganizationMembership::query()
                    ->where('organization_id', $orgId)
                    ->where('role', $oldSlug)
                    ->update(['role' => $newSlug]);

                OrganizationRolePermission::query()
                    ->where('organization_id', $orgId)
                    ->where('role', $oldSlug)
                    ->update(['role' => $newSlug]);
            });

            // Update selectedRole if it was the renamed one
            if ($this->selectedRole === $oldSlug) {
                $this->selectedRole = $newSlug;
            }
        }

        $this->editingRoleId = null;
        $this->editingRoleName = '';
        $this->loadRolePermissions();
        CacheHelper::invalidateRolePermissions($orgId);
        $this->dispatch('toast', type: 'success', message: __('settings.role_updated'));
    }

    public function deleteRole(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $role = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        if ($role->is_default) {
            $this->dispatch('toast', type: 'error', message: __('settings.role_cannot_delete_default'));
            return;
        }

        if ($role->memberCount() > 0) {
            $this->dispatch('toast', type: 'error', message: __('settings.role_cannot_delete_members'));
            return;
        }

        $deletedSlug = $role->slug;

        OrganizationRolePermission::query()
            ->where('organization_id', $orgId)
            ->where('role', $deletedSlug)
            ->delete();

        $role->delete();

        if ($this->selectedRole === $deletedSlug) {
            $this->selectedRole = 'admin';
        }

        $this->loadRolePermissions();
        CacheHelper::invalidateRolePermissions($orgId);
        $this->dispatch('toast', type: 'success', message: __('settings.role_deleted'));
    }

    public function render()
    {
        $orgId = $this->orgId();

        $org = $orgId ? Organization::query()->find($orgId) : null;

        $categories = $orgId
            ? Cache::remember(CacheHelper::categoriesKey($orgId, false), CacheHelper::TTL, function () use ($orgId) {
                return TicketCategory::query()
                    ->where('organization_id', $orgId)
                    ->orderBy('name')
                    ->get(['id', 'name', 'is_active']);
            })
            : collect();

        $priorities = $orgId
            ? Cache::remember(CacheHelper::prioritiesKey($orgId, false), CacheHelper::TTL, function () use ($orgId) {
                return TicketPriority::query()
                    ->where('organization_id', $orgId)
                    ->orderByDesc('level')
                    ->get(['id', 'name', 'level', 'is_active']);
            })
            : collect();

        $members = $orgId
            ? OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->with(['user:id,name,email'])
            ->orderBy('id')
            ->get()
            : collect();

        $organizationFunctions = $orgId
            ? Cache::remember(CacheHelper::orgFunctionsKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                return OrganizationFunction::query()
                    ->where('organization_id', $orgId)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'sort_order']);
            })
            : collect();

        $roles = $orgId
            ? RoleDefinition::query()
                ->where('organization_id', $orgId)
                ->orderByRaw("case slug when 'owner' then 0 when 'admin' then 1 when 'agent' then 2 when 'member' then 3 else 4 end")
                ->get()
            : collect();

        $roleMemberCounts = [];
        if ($orgId) {
            $counts = OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->selectRaw('role, count(*) as cnt')
                ->groupBy('role')
                ->pluck('cnt', 'role');
            foreach ($roles as $r) {
                $roleMemberCounts[$r->slug] = (int) ($counts[$r->slug] ?? 0);
            }
        }

        $maintenanceStats = $orgId ? [
            'members' => $members->count(),
            'tickets' => Ticket::query()->where('organization_id', $orgId)->count(),
            'forms' => Form::query()->where('organization_id', $orgId)->count(),
            'categories' => $categories->count(),
            'priorities' => $priorities->count(),
            'roles' => $roles->count(),
        ] : [];

        return view('livewire.admin.settings', [
            'org' => $org,
            'categories' => $categories,
            'priorities' => $priorities,
            'organizationFunctions' => $organizationFunctions,
            'members' => $members,
            'roles' => $roles,
            'roleMemberCounts' => $roleMemberCounts,
            'maintenanceStats' => $maintenanceStats,
        ]);
    }

    // ─── Maintenance ──────────────────────────────────────────────────

    public function clearOrganizationCache(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageBranding)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));
            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        CacheHelper::invalidateAll($orgId);

        // Also invalidate per-user caches for all members
        $memberIds = OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->pluck('user_id');

        foreach ($memberIds as $userId) {
            CacheHelper::invalidateNotificationsCount((int) $userId);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $userId);
            CacheHelper::invalidatePendingForms($orgId, (int) $userId);
        }

        $this->dispatch('toast', type: 'success', message: __('settings.cache_cleared'));
    }

    public function clearViewCache(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageBranding)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));
            return;
        }

        \Illuminate\Support\Facades\Artisan::call('view:clear');
        $this->dispatch('toast', type: 'success', message: __('settings.view_cache_cleared'));
    }

    // ─── Function (fonction métier) CRUD ───────────────────────────────────

    public function createFunction(): void
    {
        if (! $this->canManage) {
            abort(403);
        }
        $this->validate(['newFunctionName' => ['required', 'string', 'max:80']]);
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        OrganizationFunction::query()->create([
            'organization_id' => $orgId,
            'name' => trim($this->newFunctionName),
            'sort_order' => OrganizationFunction::query()->where('organization_id', $orgId)->max('sort_order') + 1,
        ]);
        $this->newFunctionName = '';
        CacheHelper::invalidateOrgFunctions($orgId);
        $this->dispatch('toast', type: 'success', message: __('Fonction créée.'));
    }

    public function startEditFunction(int $id): void
    {
        $orgId = $this->orgId();
        $fn = OrganizationFunction::query()->where('organization_id', $orgId)->whereKey($id)->firstOrFail();
        $this->editingFunctionId = (int) $fn->id;
        $this->editingFunctionName = (string) $fn->name;
    }

    public function cancelEditFunction(): void
    {
        $this->editingFunctionId = null;
        $this->editingFunctionName = '';
    }

    public function updateFunction(): void
    {
        if (! $this->canManage) {
            abort(403);
        }
        $this->validate(['editingFunctionName' => ['required', 'string', 'max:80']]);
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->editingFunctionId, 403);

        $fn = OrganizationFunction::query()
            ->where('organization_id', $orgId)
            ->whereKey($this->editingFunctionId)
            ->firstOrFail();
        $fn->update(['name' => trim($this->editingFunctionName)]);
        $this->editingFunctionId = null;
        $this->editingFunctionName = '';
        CacheHelper::invalidateOrgFunctions($orgId);
        $this->dispatch('toast', type: 'success', message: __('Fonction mise à jour.'));
    }

    public function deleteFunction(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }
        $orgId = $this->orgId();
        $fn = OrganizationFunction::query()->where('organization_id', $orgId)->whereKey($id)->firstOrFail();
        if ($fn->tickets()->exists()) {
            $this->dispatch('toast', type: 'error', message: __('Impossible de supprimer : des tickets sont assignés à cette fonction.'));
            return;
        }
        $fn->memberships()->update(['organization_function_id' => null]);
        $fn->delete();
        CacheHelper::invalidateOrgFunctions($orgId);
        $this->dispatch('toast', type: 'success', message: __('Fonction supprimée.'));
    }
}

