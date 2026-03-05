<?php

namespace App\Livewire\SuperAdmin;

use App\Enums\PlatformRole;
use App\Models\PlatformInvitation;
use App\Models\User;
use App\Notifications\PlatformInvitationNotification;
use App\Services\SuperAdminAuditService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.super-admin', ['title' => 'super_admin.users.title'])]
class UsersGlobal extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    public int $perPage = 20;

    // Invite modal
    public bool $showInviteModal = false;
    public string $inviteEmail = '';
    public string $inviteRole = 'platform_admin';

    // Pending invitations section
    public bool $showPendingInvitations = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function suspendUser(int $id): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $user = User::findOrFail($id);
        $user->update([
            'status' => 'deactivated',
            'deactivated_at' => now(),
        ]);

        SuperAdminAuditService::log('user.suspend', 'User', $id, [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        session()->flash('success', __('super_admin.users.suspended', ['name' => $user->name]));
    }

    public function activateUser(int $id): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $user = User::findOrFail($id);
        $user->update([
            'status' => 'active',
            'deactivated_at' => null,
        ]);

        SuperAdminAuditService::log('user.activate', 'User', $id, [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        session()->flash('success', __('super_admin.users.activated', ['name' => $user->name]));
    }

    public function forceVerifyEmail(int $id): void
    {
        if (! auth()->user()->canPlatformManage()) {
            return;
        }

        $user = User::findOrFail($id);
        $user->forceFill(['email_verified_at' => now()])->save();

        SuperAdminAuditService::log('user.force_verify', 'User', $id, [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        session()->flash('success', __('super_admin.users.email_verified', ['name' => $user->name]));
    }

    public function openInviteModal(): void
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return;
        }

        $this->inviteEmail = '';
        $this->inviteRole = 'platform_admin';
        $this->showInviteModal = true;
    }

    public function sendInvitation(): void
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return;
        }

        $this->validate([
            'inviteEmail' => 'required|email|max:255',
            'inviteRole' => 'required|in:platform_admin,platform_observer',
        ]);

        // Check if already has platform access
        $existingUser = User::where('email', $this->inviteEmail)->first();
        if ($existingUser && $existingUser->hasPlatformAccess()) {
            $this->addError('inviteEmail', __('platform_invitations.already_platform_member'));
            return;
        }

        // Check if pending invitation exists
        $existingInvitation = PlatformInvitation::pending()
            ->where('email', $this->inviteEmail)
            ->exists();

        if ($existingInvitation) {
            $this->addError('inviteEmail', __('platform_invitations.already_invited'));
            return;
        }

        $invitation = PlatformInvitation::create([
            'email' => $this->inviteEmail,
            'platform_role' => $this->inviteRole,
            'token' => Str::random(64),
            'invited_by' => auth()->id(),
            'status' => 'pending',
            'expires_at' => now()->addHours(PlatformInvitation::EXPIRY_HOURS),
        ]);

        Notification::route('mail', $this->inviteEmail)
            ->notify(new PlatformInvitationNotification($invitation));

        SuperAdminAuditService::log('platform_invitation.sent', 'User', null, [
            'email' => $this->inviteEmail,
            'platform_role' => $this->inviteRole,
        ]);

        $this->showInviteModal = false;
        $this->inviteEmail = '';
        $this->inviteRole = 'platform_admin';

        session()->flash('success', __('platform_invitations.sent'));
    }

    public function cancelInvitation(int $id): void
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return;
        }

        $invitation = PlatformInvitation::findOrFail($id);

        if ($invitation->status !== 'pending') {
            return;
        }

        $invitation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        SuperAdminAuditService::log('platform_invitation.cancelled', 'User', null, [
            'email' => $invitation->email,
            'platform_role' => $invitation->platform_role->value,
        ]);

        session()->flash('success', __('platform_invitations.cancelled'));
    }

    public function revokePlatformRole(int $userId): void
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return;
        }

        // Cannot revoke own role
        if ($userId === auth()->id()) {
            return;
        }

        $user = User::findOrFail($userId);

        if (! $user->hasPlatformAccess()) {
            return;
        }

        $oldRole = $user->platform_role?->value ?? 'super_admin (legacy)';

        $user->update([
            'platform_role' => null,
            'is_super_admin' => false,
        ]);

        SuperAdminAuditService::log('platform_role.revoked', 'User', $userId, [
            'name' => $user->name,
            'email' => $user->email,
            'old_role' => $oldRole,
        ]);

        session()->flash('success', __('platform_invitations.role_revoked', ['name' => $user->name]));
    }

    public function render()
    {
        $query = User::query()
            ->with('organizations:id,name');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                    ->orWhere('email', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        $users = $query->orderByDesc('created_at')->paginate($this->perPage);

        $pendingInvitations = PlatformInvitation::pending()
            ->with('inviter:id,name')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.super-admin.users-global', [
            'users' => $users,
            'pendingInvitations' => $pendingInvitations,
        ]);
    }
}
