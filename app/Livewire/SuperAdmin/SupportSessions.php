<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Organization;
use App\Models\SupportSession;
use App\Services\SuperAdminAuditService;
use App\Services\SupportSessionService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.super-admin', ['title' => 'super_admin.support.title'])]
class SupportSessions extends Component
{
    use WithPagination;

    public int $perPage = 15;

    // Create modal
    public bool $showCreateModal = false;
    public ?int $selectedOrgId = null;
    public string $reason = '';
    public int $duration = 15;

    public function mount(): void
    {
        // Pre-select org from query param
        if (request()->has('org')) {
            $this->selectedOrgId = (int) request()->input('org');
        }
    }

    #[Computed]
    public function activeSession(): ?SupportSession
    {
        return (new SupportSessionService)->getActive(auth()->id());
    }

    #[Computed]
    public function organizations()
    {
        return Organization::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function openCreateModal(?int $orgId = null): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $this->selectedOrgId = $orgId ?? $this->selectedOrgId;
        $this->reason = '';
        $this->duration = 15;
        $this->showCreateModal = true;
    }

    public function startSession(): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $this->validate([
            'selectedOrgId' => 'required|exists:organizations,id',
            'reason' => 'required|string|min:10|max:1000',
            'duration' => 'required|in:15,30,60',
        ]);

        $service = new SupportSessionService;
        $session = $service->start(
            auth()->id(),
            $this->selectedOrgId,
            $this->reason,
            $this->duration,
        );

        $org = Organization::find($this->selectedOrgId);

        SuperAdminAuditService::log('support_session.start', 'Organization', $this->selectedOrgId, [
            'org_name' => $org->name,
            'reason' => $this->reason,
            'duration_minutes' => $this->duration,
            'session_id' => $session->id,
        ]);

        session()->put('current_organization_id', $org->id);
        session()->put('support_session_id', $session->id);

        $this->showCreateModal = false;

        $this->redirect(route('dashboard'));
    }

    public function endSession(int $sessionId): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $session = SupportSession::findOrFail($sessionId);
        $service = new SupportSessionService;
        $service->end($session);

        SuperAdminAuditService::log('support_session.end', 'Organization', $session->organization_id, [
            'org_name' => $session->organization?->name,
            'session_id' => $session->id,
        ]);

        session()->forget('current_organization_id');
        session()->forget('support_session_id');

        session()->flash('success', __('super_admin.support.ended'));

        unset($this->activeSession);
    }

    public function render()
    {
        $sessions = SupportSession::query()
            ->with(['user:id,name', 'organization:id,name'])
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.super-admin.support-sessions', [
            'sessions' => $sessions,
        ]);
    }
}
