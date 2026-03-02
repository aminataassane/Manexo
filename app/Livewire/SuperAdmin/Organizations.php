<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Organization;
use App\Services\SuperAdminAuditService;
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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function activateOrg(int $id): void
    {
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
        $this->suspendOrgId = $id;
        $this->suspendReason = '';
        $this->showSuspendModal = true;
    }

    public function confirmSuspend(): void
    {
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
