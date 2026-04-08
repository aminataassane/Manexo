<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Models\Organization;
use App\Models\OrganizationFunction;
use App\Models\OrganizationInvitation;
use App\Models\OrganizationMembership;
use App\Models\RoleDefinition;
use App\Models\User;
use App\Notifications\OrganizationInvitationNotification;
use App\Notifications\TeamRoleChangedNotification;
use App\Services\OrganizationAuditService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

#[Layout('layouts.manexo-app')]
#[Title('Équipe')]
class Users extends Component
{
    use WithPagination;

    public bool $ready = false;

    public function loadPage(): void
    {
        $this->ready = true;
    }

    public string $search = '';

    public string $searchExternal = '';

    public string $role = '';

    public int $perPage = 10;

    public bool $showInviteModal = false;

    public string $inviteEmail = '';

    public string $inviteRole = '';

    public string $inviteMethod = 'email';

    public string $generatedInviteCode = '';

    /** internal | external — les contacts externes ne sont chargés qu’en ouvrant l’onglet (perf). */
    public string $teamTab = 'internal';

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

    public function updatedSearchExternal(): void
    {
        $this->resetPage('externalPage');
    }

    public function setTeamTab(string $tab): void
    {
        $this->teamTab = in_array($tab, ['internal', 'external'], true) ? $tab : 'internal';
    }

    private function orgId(): int
    {
        return (int) session('current_organization_id');
    }

    private function currentRole(): string
    {
        $user = Auth::user();
        $orgId = $this->orgId();

        if (! $user instanceof User || ! $orgId) {
            return OrganizationRole::Member->value;
        }

        return (string) Cache::remember("admin_users_current_role:{$orgId}:{$user->id}", 120, function () use ($user, $orgId) {
            return (string) (OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->where('user_id', $user->id)
                ->value('role') ?? OrganizationRole::Member->value);
        });
    }

    private function allowedRoleSlugs(int $orgId): array
    {
        return Cache::remember("org_role_slugs:{$orgId}", CacheHelper::TTL_CONFIG, function () use ($orgId) {
            return RoleDefinition::query()
                ->where('organization_id', $orgId)
                ->pluck('slug')
                ->all();
        });
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
        $this->inviteMethod = 'email';
        $this->generatedInviteCode = '';
        $this->showInviteModal = true;
    }

    public function closeInviteModal(): void
    {
        $this->showInviteModal = false;
        $this->generatedInviteCode = '';
        $this->resetErrorBag();
    }

    public function sendInvite(?string $inviteMethodOverride = null): void
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        if (! $authUser || ! $authUser->hasPermission(Permission::TeamInvite)) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        if (in_array($inviteMethodOverride, ['email', 'code'], true)) {
            $this->inviteMethod = $inviteMethodOverride;
        }

        $allowedRoles = $this->allowedRoleSlugs($orgId);

        $validated = $this->validate([
            'inviteMethod' => ['required', 'in:email,code'],
            'inviteEmail' => ['nullable', 'email', 'max:255'],
            'inviteRole' => ['required', 'string', 'in:'.implode(',', $allowedRoles)],
        ]);

        $method = (string) $validated['inviteMethod'];
        $email = $method === 'email'
            ? Str::lower(trim((string) ($validated['inviteEmail'] ?? '')))
            : null;
        $role = (string) $validated['inviteRole'];

        if ($method === 'email' && empty($email)) {
            $this->addError('inviteEmail', __('validation.required', ['attribute' => __('pages.team.email')]));

            return;
        }

        // Check if user is already a member
        $existingUser = null;
        if ($email) {
            $existingUser = User::query()->where('email', $email)->first();
        }

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
        $existingInvitation = false;
        if ($email) {
            $existingInvitation = OrganizationInvitation::query()
                ->where('organization_id', $orgId)
                ->where('email', $email)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->exists();
        }

        if ($existingInvitation) {
            $this->addError('inviteEmail', __('pages.team.invitation_already_pending'));

            return;
        }

        // Cancel any old pending invitations for this email+org (expired ones)
        if ($email) {
            OrganizationInvitation::query()
                ->where('organization_id', $orgId)
                ->where('email', $email)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);
        }

        $inviteCode = $method === 'code' ? $this->createUniqueInvitationCode() : null;

        $invitation = OrganizationInvitation::create([
            'organization_id' => $orgId,
            'email' => $email,
            'role' => $role,
            'token' => Str::random(64),
            'invitation_code' => $inviteCode,
            'invited_by' => (int) $authUser->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(OrganizationInvitation::EXPIRY_DAYS),
        ]);

        $org = Cache::remember("org_name:{$orgId}", CacheHelper::TTL_CONFIG, fn () => Organization::query()->find($orgId));

        OrganizationAuditService::log('team.invitation_sent', 'User', null, [
            'email' => $email,
            'role' => $role,
        ]);

        if ($method === 'email') {
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
        } else {
            $this->generatedInviteCode = (string) $inviteCode;
            $this->dispatch('toast', type: 'success', message: __('pages.team.invitation_code_created'));
        }

        $this->inviteEmail = '';
        $this->inviteMethod = 'email';
        $this->inviteRole = OrganizationRole::Member->value;
        $this->resetPage();
        $this->forgetTeamPageCaches($orgId);
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
            'invitation_code' => $invitation->email ? null : $this->createUniqueInvitationCode(),
            'expires_at' => now()->addDays(OrganizationInvitation::EXPIRY_DAYS),
        ]);

        if (! $invitation->email) {
            $this->generatedInviteCode = (string) $invitation->invitation_code;
            $this->dispatch('toast', type: 'success', message: __('pages.team.invitation_code_regenerated'));
            $this->forgetTeamPageCaches($orgId);

            return;
        }

        $org = Cache::remember("org_name:{$orgId}", CacheHelper::TTL_CONFIG, fn () => Organization::query()->find($orgId));
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

        $this->forgetTeamPageCaches($orgId);
    }

    private function createUniqueInvitationCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (OrganizationInvitation::query()
            ->where('invitation_code', $code)
            ->exists());

        return $code;
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
        $this->forgetTeamPageCaches($orgId);
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
                $this->dispatch('toast', type: 'error', message: 'Impossible: il faut au moins un propriétaire.');

                return;
            }
        }

        $oldRole = $membership->role;
        $membership->update(['role' => $newRole]);
        $targetUser = User::find((int) $membership->user_id);
        $orgName = (string) (Organization::query()->whereKey($orgId)->value('name') ?? '');
        if ($targetUser && (int) $targetUser->id !== (int) $user->id) {
            $targetUser->notify(new TeamRoleChangedNotification(
                organizationId: $orgId,
                organizationName: $orgName,
                oldRole: (string) $oldRole,
                newRole: $newRole,
                changedByName: (string) $user->name,
            ));
            event(new UserNotificationReceived(userId: (int) $targetUser->id, notificationType: 'team_role_changed'));
        }
        OrganizationAuditService::log('team.role_changed', 'User', $membership->user_id, [
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'user_name' => $membership->user?->name,
        ]);
        $this->dispatch('toast', type: 'success', message: 'Rôle mis à jour.');
        Cache::forget("admin_users_current_role:{$orgId}:{$membership->user_id}");
        Cache::forget("admin_users_stats:{$orgId}");
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
                $this->dispatch('toast', type: 'error', message: 'Impossible: il faut au moins un propriétaire.');

                return;
            }
        }

        OrganizationAuditService::log('team.member_removed', 'User', $membership->user_id, [
            'user_name' => $membership->user?->name,
            'role' => $membership->role,
        ]);
        $removedUserId = (int) $membership->user_id;
        $membership->delete();
        $this->dispatch('toast', type: 'success', message: 'Membre retiré.');
        $this->resetPage();
        Cache::forget("admin_users_current_role:{$orgId}:{$removedUserId}");
        Cache::forget("admin_users_stats:{$orgId}");
    }

    public function render()
    {
        return $this->renderUsersPage();
    }

    private function renderUsersPage()
    {
        $user = Auth::user();
        $orgId = $this->orgId();

        if (! $this->canRenderTeamPage($user, $orgId)) {
            return redirect()->route('organizations.select');
        }

        // Skeleton: render the page shell immediately, data loads via wire:init
        if (! $this->ready) {
            return view('livewire.admin.users', [
                'memberships' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'stats' => ['total' => 0, 'owners' => 0, 'admins' => 0, 'agents' => 0, 'members' => 0, 'external' => 0, 'pending_invites' => 0],
                'currentRole' => 'member',
                'organizationFunctions' => collect(),
                'roles' => collect(),
                'pendingInvitations' => new EloquentCollection,
                'externalContacts' => $this->emptyExternalPaginator(),
                'teamTab' => $this->teamTab,
            ]);
        }

        $currentRole = $this->currentRole();
        $memberships = $this->membershipsPaginator($orgId);
        $stats = $this->teamStats($orgId);
        $pendingInvitations = $this->pendingInvitations($orgId, (int) ($stats['pending_invites'] ?? 0));
        $organizationFunctions = $this->organizationFunctions($orgId);
        $roles = $this->organizationRoles($orgId);
        $externalContacts = $this->teamTab === 'external'
            ? $this->externalContacts($orgId)
            : $this->emptyExternalPaginator();

        return view('livewire.admin.users', [
            'memberships' => $memberships,
            'stats' => $stats,
            'currentRole' => $currentRole,
            'organizationFunctions' => $organizationFunctions,
            'roles' => $roles,
            'pendingInvitations' => $pendingInvitations,
            'externalContacts' => $externalContacts,
            'teamTab' => $this->teamTab,
        ]);
    }

    private function canRenderTeamPage(mixed $user, int $orgId): bool
    {
        if (! $user || ! $orgId) {
            return false;
        }
        if (! ($user instanceof User) || ! $user->hasAnyPermission([Permission::TeamInvite, Permission::TeamEditRole, Permission::TeamRemove])) {
            abort(403);
        }

        return true;
    }

    private function membershipsPaginator(int $orgId)
    {
        $search = trim($this->search);

        return OrganizationMembership::query()
            ->select([
                'organization_memberships.id',
                'organization_memberships.organization_id',
                'organization_memberships.user_id',
                'organization_memberships.role',
                'organization_memberships.organization_function_id',
                'organization_memberships.created_at',
            ])
            ->join('users', 'users.id', '=', 'organization_memberships.user_id')
            ->where('organization_memberships.organization_id', $orgId)
            ->where('users.status', '!=', 'guest')
            ->when($this->role !== '', fn ($q) => $q->where('organization_memberships.role', $this->role))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('users.name', 'ilike', "%{$search}%")
                        ->orWhere('users.email', 'ilike', "%{$search}%");
                });
            })
            ->with([
                'user:id,name,email',
                'organizationFunction:id,name',
            ])
            ->orderByRaw("case organization_memberships.role when 'owner' then 0 when 'admin' then 1 when 'agent' then 2 else 3 end")
            ->orderByDesc('organization_memberships.created_at')
            ->paginate($this->perPage);
    }

    private function teamStats(int $orgId): array
    {
        return Cache::remember("admin_users_stats:{$orgId}", 120, function () use ($orgId) {
            // Single raw query: member stats + external + pending invites in one DB call
            $row = DB::selectOne("
                SELECT
                    count(*) FILTER (WHERE u.status != 'guest') AS total,
                    count(*) FILTER (WHERE om.role = 'owner' AND u.status != 'guest') AS owners,
                    count(*) FILTER (WHERE om.role = 'admin' AND u.status != 'guest') AS admins,
                    count(*) FILTER (WHERE om.role = 'agent' AND u.status != 'guest') AS agents,
                    count(*) FILTER (WHERE om.role = 'member' AND u.status != 'guest') AS members,
                    count(*) FILTER (WHERE u.status = 'guest') AS external,
                    (SELECT count(*) FROM organization_invitations
                     WHERE organization_id = ? AND status = 'pending' AND expires_at > NOW()
                    ) AS pending_invites
                FROM organization_memberships om
                JOIN users u ON u.id = om.user_id
                WHERE om.organization_id = ?
            ", [$orgId, $orgId]);

            return [
                'total' => (int) ($row->total ?? 0),
                'owners' => (int) ($row->owners ?? 0),
                'admins' => (int) ($row->admins ?? 0),
                'agents' => (int) ($row->agents ?? 0),
                'members' => (int) ($row->members ?? 0),
                'external' => (int) ($row->external ?? 0),
                'pending_invites' => (int) ($row->pending_invites ?? 0),
            ];
        });
    }

    /**
     * @param  int  $pendingCount  depuis teamStats (évite une requête si 0)
     */
    private function pendingInvitations(int $orgId, int $pendingCount): EloquentCollection
    {
        if ($pendingCount === 0) {
            return new EloquentCollection;
        }

        return Cache::remember("admin_users_pending_invites:{$orgId}", 60, function () use ($orgId) {
            return OrganizationInvitation::query()
                ->with('inviter')
                ->where('organization_id', $orgId)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->orderByDesc('created_at')
                ->get();
        });
    }

    private function emptyExternalPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            [],
            0,
            $this->perPage,
            Paginator::resolveCurrentPage('externalPage', 1),
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'externalPage',
            ]
        );
    }

    private function forgetTeamPageCaches(int $orgId): void
    {
        Cache::forget("admin_users_pending_invites:{$orgId}");
        Cache::forget("admin_users_stats:{$orgId}");
    }

    private function externalContacts(int $orgId)
    {
        $search = trim($this->searchExternal);

        return OrganizationMembership::query()
            ->select('organization_memberships.*')
            ->join('users', 'users.id', '=', 'organization_memberships.user_id')
            ->where('organization_memberships.organization_id', $orgId)
            ->where('users.status', 'guest')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('users.name', 'ilike', "%{$search}%")
                        ->orWhere('users.email', 'ilike', "%{$search}%");
                });
            })
            ->with('user')
            ->orderByDesc('organization_memberships.created_at')
            ->paginate($this->perPage, ['*'], 'externalPage');
    }

    private function organizationFunctions(int $orgId)
    {
        return Cache::remember("org_functions:{$orgId}", CacheHelper::TTL_CONFIG, fn () => OrganizationFunction::query()
            ->where('organization_id', $orgId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
        );
    }

    private function organizationRoles(int $orgId)
    {
        return Cache::remember("org_roles:{$orgId}", CacheHelper::TTL_CONFIG, fn () => RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->orderByRaw("case slug when 'owner' then 0 when 'admin' then 1 when 'agent' then 2 when 'member' then 3 else 4 end")
            ->get()
        );
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
