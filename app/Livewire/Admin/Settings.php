<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Models\Organization;
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

        $this->name = (string) $org->name;
        $this->slug = (string) $org->slug;
        $this->primary_color = $org->primary_color ?: null;
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

    public function save()
    {
        if (! $this->canManage) {
            session()->flash('settings_status', "Accès refusé: réservé aux admins.");
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:organizations,slug,' . $this->orgId()],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'default_category_id' => ['nullable', 'integer'],
            'default_priority_id' => ['nullable', 'integer'],
            'auto_close_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'members_can_edit' => ['boolean'],
            'members_can_delete' => ['boolean'],
        ]);

        $org = $this->orgOrFail();

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

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $this->currentLogoUrl = $org->logo_path ? $disk->url($org->logo_path) : null;
        $this->logo = null;

        session()->flash('settings_status', "Paramètres mis à jour.");

        // Refresh accent color / shared org object
        return $this->redirectRoute('admin.settings', navigate: true);
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

        $this->redirectRoute('organizations.select', navigate: true);
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

        return view('livewire.admin.settings', [
            'org' => $org,
            'categories' => $categories,
            'priorities' => $priorities,
        ]);
    }
}

