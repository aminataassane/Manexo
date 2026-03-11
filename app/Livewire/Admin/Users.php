<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Models\Organization;
use App\Models\OrganizationFunction;
use App\Models\OrganizationInvitation;
use App\Models\OrganizationMembership;
use App\Models\RoleDefinition;
use App\Models\User;
use App\Notifications\OrganizationInvitationNotification;
use App\Events\UserNotificationReceived;
use App\Services\OrganizationAuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Throwable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.manexo-app')]
#[Title("Équipe")]
class Users extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = '';
    public int $perPage = 10;

    public bool $showInviteModal = false;
    public string $inviteEmail = '';
    public string $inviteRole = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

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

    public function openInviteModal(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::TeamInvite)) {
            abort(403);
        }

        $this->resetErrorBag();
        $this->inviteEmail = '';
        $this->inviteRole = OrganizationRole::Member->value;
        $this->showInviteModal = true;
    }

    public function closeInviteModal(): void
    {
        $this->showInviteModal = false;
        $this->resetErrorBag();
    }

    public function sendInvite(): void
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        if (! $authUser || ! $authUser->hasPermission(Permission::TeamInvite)) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $allowedRoles = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->pluck('slug')
            ->all();

        $validated = $this->validate([
            'inviteEmail' => ['required', 'email', 'max:255'],
            'inviteRole' => ['required', 'string', 'in:' . implode(',', $allowedRoles)],
        ]);

        $email = Str::lower(trim((string) $validated['inviteEmail']));
        $role = (string) $validated['inviteRole'];

        // Check if user is already a member
        $existingUser = User::query()->where('email', $email)->first();
        if ($existingUser) {
            $alreadyMember = OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->where('user_id', (int) $existingUser->id)
                ->exists();

            if ($alreadyMember) {
                $this->addError('inviteEmail', __('pages.team.already_member'));
                return;
            }
        }

        // Check if a pending invitation already exists
        $existingInvitation = OrganizationInvitation::query()
            ->where('organization_id', $orgId)
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->exists();

        if ($existingInvitation) {
            $this->addError('inviteEmail', __('pages.team.invitation_already_pending'));
            return;
        }

        // Cancel any old pending invitations for this email+org (expired ones)
        OrganizationInvitation::query()
            ->where('organization_id', $orgId)
            ->where('email', $email)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        $invitation = OrganizationInvitation::create([
            'organization_id' => $orgId,
            'email' => $email,
            'role' => $role,
            'token' => Str::random(64),
            'invited_by' => (int) $authUser->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(OrganizationInvitation::EXPIRY_DAYS),
        ]);

        $org = Organization::find($orgId);

        OrganizationAuditService::log('team.invitation_sent', 'User', null, [
            'email' => $email,
            'role' => $role,
        ]);

        $notification = new OrganizationInvitationNotification($invitation, $org);

        try {
            if ($existingUser) {
                $existingUser->notify($notification);
                event(new UserNotificationReceived(userId: (int) $existingUser->id, notificationType: 'organization_invitation'));
            } else {
                Notification::route('mail', $email)->notify($notification);
            }
            $this->dispatch('toast', type: 'success', message: __('pages.team.invitation_sent'));
        } catch (Throwable $e) {
            Log::warning('Organization invitation email could not be sent.', [
                'email' => $email,
                'organization_id' => $orgId,
                'exception' => $e->getMessage(),
            ]);
            $this->dispatch('toast', type: 'success', message: __('pages.team.invitation_created_email_failed'));
        }

        $this->showInviteModal = false;
        $this->inviteEmail = '';
        $this->inviteRole = OrganizationRole::Member->value;
        $this->resetPage();
    }

    public function resendInvitation(int $invitationId): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::TeamInvite)) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $invitation = OrganizationInvitation::query()
            ->where('organization_id', $orgId)
            ->where('status', 'pending')
            ->findOrFail($invitationId);

        $invitation->update([
            'token' => Str::random(64),
            'expires_at' => now()->addDays(OrganizationInvitation::EXPIRY_DAYS),
        ]);

        $org = Organization::find($orgId);
        $notification = new OrganizationInvitationNotification($invitation, $org);
        $existingUser = User::query()->where('email', $invitation->email)->first();

        try {
            if ($existingUser) {
                $existingUser->notify($notification);
                event(new UserNotificationReceived(userId: (int) $existingUser->id, notificationType: 'organization_invitation'));
            } else {
                Notification::route('mail', $invitation->email)->notify($notification);
            }
            $this->dispatch('toast', type: 'success', message: __('pages.team.invitation_resent'));
        } catch (Throwable $e) {
            Log::warning('Organization invitation resend email could not be sent.', [
                'email' => $invitation->email,
                'invitation_id' => $invitation->id,
                'exception' => $e->getMessage(),
            ]);
            $this->dispatch('toast', type: 'warning', message: __('pages.team.invitation_resent_email_failed'));
        }
    }

    public function cancelInvitation(int $invitationId): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::TeamInvite)) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $invitation = OrganizationInvitation::query()
            ->where('organization_id', $orgId)
            ->where('status', 'pending')
            ->findOrFail($invitationId);

        $invitation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $this->dispatch('toast', type: 'success', message: __('pages.team.invitation_cancelled'));
    }

    public function updateRole(int $membershipId, string $newRole): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::TeamEditRole)) {
            abort(403);
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            abort(403);
        }

        $allowed = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->pluck('slug')
            ->all();
        if (! in_array($newRole, $allowed, true)) {
            return;
        }

        $membership = OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->whereKey($membershipId)
            ->firstOrFail();

        // Only owners can promote someone to owner
        if ($newRole === OrganizationRole::Owner->value && $this->currentRole() !== OrganizationRole::Owner->value) {
            abort(403);
        }

        // Prevent removing the last owner role
        if ($membership->role === OrganizationRole::Owner->value && $newRole !== OrganizationRole::Owner->value) {
            $ownersCount = OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->where('role', OrganizationRole::Owner->value)
                ->count();

            if ($ownersCount <= 1) {
                $this->dispatch('toast', type: 'error', message: "Impossible: il faut au moins un propriétaire.");
                return;
            }
        }

        $oldRole = $membership->role;
        $membership->update(['role' => $newRole]);
        OrganizationAuditService::log('team.role_changed', 'User', $membership->user_id, [
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'user_name' => $membership->user?->name,
        ]);
        $this->dispatch('toast', type: 'success', message: "Rôle mis à jour.");
    }

    public function removeMember(int $membershipId): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::TeamRemove)) {
            abort(403);
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            abort(403);
        }

        $membership = OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->whereKey($membershipId)
            ->firstOrFail();

        // Admins can't remove owners
        if ($membership->role === OrganizationRole::Owner->value && $this->currentRole() !== OrganizationRole::Owner->value) {
            abort(403);
        }

        // Prevent removing the last owner
        if ($membership->role === OrganizationRole::Owner->value) {
            $ownersCount = OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->where('role', OrganizationRole::Owner->value)
                ->count();

            if ($ownersCount <= 1) {
                $this->dispatch('toast', type: 'error', message: "Impossible: il faut au moins un propriétaire.");
                return;
            }
        }

        OrganizationAuditService::log('team.member_removed', 'User', $membership->user_id, [
            'user_name' => $membership->user?->name,
            'role' => $membership->role,
        ]);
        $membership->delete();
        $this->dispatch('toast', type: 'success', message: "Membre retiré.");
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $orgId = $this->orgId();

        if (! $user || ! $orgId) {
            return redirect()->route('organizations.select');
        }

        $currentRole = $this->currentRole();

        // Restrict access to users with team permissions
        if (! $user->hasAnyPermission([Permission::TeamInvite, Permission::TeamEditRole, Permission::TeamRemove])) {
            abort(403);
        }

        $search = trim($this->search);

        $membershipsQuery = OrganizationMembership::query()
            ->with(['user', 'organizationFunction'])
            ->where('organization_id', $orgId)
            ->when($this->role !== '', fn($q) => $q->where('role', $this->role))
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%");
                });
            })
            ->orderByRaw("case role when 'owner' then 0 when 'admin' then 1 when 'agent' then 2 else 3 end")
            ->orderByDesc('created_at');

        $memberships = $membershipsQuery->paginate($this->perPage);

        $statsRow = OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->selectRaw('count(*) as total')
            ->selectRaw("count(*) filter (where role = 'owner') as owners")
            ->selectRaw("count(*) filter (where role = 'admin') as admins")
            ->selectRaw("count(*) filter (where role = 'agent') as agents")
            ->selectRaw("count(*) filter (where role = 'member') as members")
            ->first();

        $stats = [
            'total' => (int) ($statsRow?->total ?? 0),
            'owners' => (int) ($statsRow?->owners ?? 0),
            'admins' => (int) ($statsRow?->admins ?? 0),
            'agents' => (int) ($statsRow?->agents ?? 0),
            'members' => (int) ($statsRow?->members ?? 0),
        ];

        // Load pending invitations
        $pendingInvitations = OrganizationInvitation::query()
            ->with('inviter')
            ->where('organization_id', $orgId)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->get();

        $organizationFunctions = OrganizationFunction::query()
            ->where('organization_id', $orgId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $roles = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->orderByRaw("case slug when 'owner' then 0 when 'admin' then 1 when 'agent' then 2 when 'member' then 3 else 4 end")
            ->get();

        return view('livewire.admin.users', [
            'memberships' => $memberships,
            'stats' => $stats,
            'currentRole' => $currentRole,
            'organizationFunctions' => $organizationFunctions,
            'roles' => $roles,
            'pendingInvitations' => $pendingInvitations,
        ]);
    }

    public function updateFunction(int $membershipId, ?string $functionId): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::TeamEditRole)) {
            abort(403);
        }
        $orgId = $this->orgId();
        if (! $orgId) {
            abort(403);
        }
        $membership = OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->whereKey($membershipId)
            ->firstOrFail();
        $id = $functionId === '' || $functionId === null ? null : (int) $functionId;
        if ($id !== null) {
            OrganizationFunction::query()
                ->where('organization_id', $orgId)
                ->whereKey($id)
                ->firstOrFail();
        }
        $membership->update(['organization_function_id' => $id]);
        $this->dispatch('toast', type: 'success', message: __('Fonction mise à jour.'));
    }
}
