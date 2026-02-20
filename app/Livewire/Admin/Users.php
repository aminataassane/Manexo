<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Models\OrganizationFunction;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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

    private function canManage(): bool
    {
        return in_array($this->currentRole(), [OrganizationRole::Owner->value, OrganizationRole::Admin->value], true);
    }

    public function openInviteModal(): void
    {
        if (! $this->canManage()) {
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
        if (! $this->canManage()) {
            abort(403);
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $allowedRoles = array_map(fn($r) => $r->value, OrganizationRole::cases());

        $validated = $this->validate([
            'inviteEmail' => ['required', 'email', 'max:255'],
            'inviteRole' => ['required', 'string', 'in:' . implode(',', $allowedRoles)],
        ]);

        $email = Str::lower(trim((string) $validated['inviteEmail']));
        $role = (string) $validated['inviteRole'];

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => Str::of($email)->before('@')->replace(['.', '_', '-'], ' ')->title()->toString(),
                'email' => $email,
                'password' => Str::random(32),
            ]);
        }

        $exists = OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->where('user_id', (int) $user->id)
            ->exists();

        if ($exists) {
            $this->addError('inviteEmail', 'Cet utilisateur est déjà membre de l’organisation.');
            return;
        }

        OrganizationMembership::query()->create([
            'organization_id' => $orgId,
            'user_id' => (int) $user->id,
            'role' => $role,
        ]);

        // Best-effort: send a password reset email to let the user set a password.
        Password::sendResetLink(['email' => $email]);

        $this->showInviteModal = false;
        $this->inviteEmail = '';
        $this->inviteRole = OrganizationRole::Member->value;

        $this->dispatch('toast', type: 'success', message: 'Invitation envoyée.');
        $this->resetPage();
    }

    public function updateRole(int $membershipId, string $newRole): void
    {
        if (! $this->canManage()) {
            abort(403);
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            abort(403);
        }

        $allowed = array_map(fn($r) => $r->value, OrganizationRole::cases());
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

        $membership->update(['role' => $newRole]);
        $this->dispatch('toast', type: 'success', message: "Rôle mis à jour.");
    }

    public function removeMember(int $membershipId): void
    {
        if (! $this->canManage()) {
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

        // Restrict access to owner/admin
        if (! in_array($currentRole, [OrganizationRole::Owner->value, OrganizationRole::Admin->value], true)) {
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

        $organizationFunctions = OrganizationFunction::query()
            ->where('organization_id', $orgId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.users', [
            'memberships' => $memberships,
            'stats' => $stats,
            'currentRole' => $currentRole,
            'organizationFunctions' => $organizationFunctions,
        ]);
    }

    public function updateFunction(int $membershipId, ?string $functionId): void
    {
        if (! $this->canManage()) {
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
