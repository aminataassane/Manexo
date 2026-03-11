<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Scopes\OrganizationScope;
use App\Models\User;
use App\Services\SuperAdminAuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.super-admin', ['title' => 'super_admin.organizations.title'])]
class Organizations extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    public int $perPage = 15;

    public bool $showSuspendModal = false;
    public ?int $suspendOrgId = null;
    public string $suspendReason = '';

    public bool $showEnterModal = false;
    public ?int $enterOrgId = null;

    public bool $showActivateModal = false;
    public ?int $activateOrgId = null;

    public bool $showDisableModal = false;
    public ?int $disableOrgId = null;

    // Create modal
    public bool $showCreateModal = false;
    public string $createName = '';
    public string $createSlug = '';
    public string $createOwnerEmail = '';

    // Archive modal
    public bool $showArchiveModal = false;
    public ?int $archiveOrgId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function stats(): array
    {
        $orgCounts = DB::table('organizations')
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw("COUNT(*) FILTER (WHERE status = 'active' OR status IS NULL) as active"),
                DB::raw("COUNT(*) FILTER (WHERE status = 'suspended') as suspended"),
                DB::raw("COUNT(*) FILTER (WHERE status = 'disabled') as disabled"),
                DB::raw("COUNT(*) FILTER (WHERE created_at >= NOW() - INTERVAL '30 days') as created_30d"),
            ])
            ->first();

        $totalMembers = (int) DB::table('organization_memberships')->count();

        $totalTickets = 0;
        try {
            $totalTickets = (int) DB::table('tickets')->count();
        } catch (\Throwable) {
        }

        return [
            'total_orgs' => (int) $orgCounts->total,
            'active_orgs' => (int) $orgCounts->active,
            'suspended_orgs' => (int) $orgCounts->suspended,
            'disabled_orgs' => (int) $orgCounts->disabled,
            'created_30d' => (int) $orgCounts->created_30d,
            'total_members' => $totalMembers,
            'total_tickets' => $totalTickets,
        ];
    }

    public function updatedCreateName(): void
    {
        $this->createSlug = Str::slug($this->createName);
    }

    public function openCreateModal(): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $this->createName = '';
        $this->createSlug = '';
        $this->createOwnerEmail = '';
        $this->showCreateModal = true;
    }

    public function confirmCreate(): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $this->validate([
            'createName' => 'required|string|min:2|max:255',
            'createSlug' => 'required|string|min:2|max:255|unique:organizations,slug',
            'createOwnerEmail' => 'required|email|exists:users,email',
        ]);

        $owner = User::where('email', $this->createOwnerEmail)->firstOrFail();

        $org = Organization::create([
            'name' => $this->createName,
            'slug' => $this->createSlug,
            'created_by' => $owner->id,
            'status' => 'active',
        ]);

        // Add owner as member
        OrganizationMembership::create([
            'organization_id' => $org->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        SuperAdminAuditService::log('org.create', 'Organization', $org->id, [
            'name' => $org->name,
            'slug' => $org->slug,
            'owner_email' => $this->createOwnerEmail,
        ]);

        $this->showCreateModal = false;
        $this->createName = '';
        $this->createSlug = '';
        $this->createOwnerEmail = '';

        session()->flash('success', __('super_admin.organizations.create_success', ['name' => $org->name]));
    }

    public function openArchiveModal(int $id): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $this->archiveOrgId = $id;
        $this->showArchiveModal = true;
    }

    public function confirmArchive(): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $org = Organization::findOrFail($this->archiveOrgId);
        $org->update([
            'archived_at' => now(),
            'status' => 'disabled',
        ]);

        SuperAdminAuditService::log('org.archive', 'Organization', $org->id, [
            'name' => $org->name,
        ]);

        $this->showArchiveModal = false;
        $this->archiveOrgId = null;

        session()->flash('success', __('super_admin.organizations.archived', ['name' => $org->name]));
    }

    public function activateOrg(int $id): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $org = Organization::findOrFail($id);
        $org->update([
            'status' => 'active',
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        SuperAdminAuditService::log('org.activate', 'Organization', $id, [
            'name' => $org->name,
        ]);

        session()->flash('success', __('super_admin.organizations.activated', ['name' => $org->name]));
    }

    public function openSuspendModal(int $id): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $this->suspendOrgId = $id;
        $this->suspendReason = '';
        $this->showSuspendModal = true;
    }

    public function confirmSuspend(): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $org = Organization::findOrFail($this->suspendOrgId);
        $org->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $this->suspendReason ?: null,
        ]);

        SuperAdminAuditService::log('org.suspend', 'Organization', $org->id, [
            'name' => $org->name,
            'reason' => $this->suspendReason,
        ]);

        $this->showSuspendModal = false;
        $this->suspendOrgId = null;
        $this->suspendReason = '';

        session()->flash('success', __('super_admin.organizations.suspended', ['name' => $org->name]));
    }

    public function openEnterModal(int $id): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $this->enterOrgId = $id;
        $this->showEnterModal = true;
    }

    public function confirmEnter(): void
    {
        if ($this->enterOrgId) {
            $this->enterOrganization($this->enterOrgId);
        }
        $this->showEnterModal = false;
        $this->enterOrgId = null;
    }

    public function enterOrganization(int $id): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $org = Organization::find($id);

        if (! $org || ! $org->isActive()) {
            session()->flash('error', __('super_admin.organizations.cannot_enter_inactive'));
            return;
        }

        session()->put('current_organization_id', $org->id);

        SuperAdminAuditService::log('org.enter', 'Organization', $id, [
            'name' => $org->name,
        ]);

        $this->redirect(route('dashboard'));
    }

    public function openActivateModal(int $id): void
    {
        $this->activateOrgId = $id;
        $this->showActivateModal = true;
    }

    public function confirmActivate(): void
    {
        if ($this->activateOrgId) {
            $this->activateOrg($this->activateOrgId);
        }
        $this->showActivateModal = false;
        $this->activateOrgId = null;
    }

    public function openDisableModal(int $id): void
    {
        $this->disableOrgId = $id;
        $this->showDisableModal = true;
    }

    public function confirmDisable(): void
    {
        if ($this->disableOrgId) {
            $this->disableOrg($this->disableOrgId);
        }
        $this->showDisableModal = false;
        $this->disableOrgId = null;
    }

    public function disableOrg(int $id): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $org = Organization::findOrFail($id);
        $org->update([
            'status' => 'disabled',
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        SuperAdminAuditService::log('org.disable', 'Organization', $id, [
            'name' => $org->name,
        ]);

        session()->flash('success', __('super_admin.organizations.disabled', ['name' => $org->name]));
    }

    public function render()
    {
        $query = Organization::query()
            ->withCount('memberships')
            ->withCount(['tickets' => fn ($q) => $q->withoutGlobalScope(OrganizationScope::class)])
            ->with('creator:id,name');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('slug', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        $organizations = $query->orderByDesc('created_at')->paginate($this->perPage);

        return view('livewire.super-admin.organizations', [
            'organizations' => $organizations,
        ]);
    }
}
