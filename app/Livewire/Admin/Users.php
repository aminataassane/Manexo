<?php

namespace App\Livewire\Admin;

use App\Enums\OrganizationRole;
use App\Models\OrganizationMembership;
use Illuminate\Support\Facades\Auth;
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

    public function updateRole(int $membershipId, string $newRole): void
    {
        if (! $this->canManage()) {
            abort(403);
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            abort(403);
        }

        $allowed = array_map(fn ($r) => $r->value, OrganizationRole::cases());
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
            ->with('user')
            ->where('organization_id', $orgId)
            ->when($this->role !== '', fn ($q) => $q->where('role', $this->role))
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

        return view('livewire.admin.users', [
            'memberships' => $memberships,
            'stats' => $stats,
            'currentRole' => $currentRole,
        ]);
    }
}

