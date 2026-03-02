<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Organization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.super-admin', ['title' => 'super_admin.org_detail.title'])]
class OrganizationDetail extends Component
{
    use WithPagination;

    public Organization $organization;

    #[Url]
    public string $memberSearch = '';

    public function updatingMemberSearch(): void
    {
        $this->resetPage();
    }

    public function mount(Organization $organization): void
    {
        $this->organization = $organization;
    }

    public function render()
    {
        $membersQuery = $this->organization->users();

        if ($this->memberSearch !== '') {
            $membersQuery->where(function ($q) {
                $q->where('users.name', 'ilike', '%' . $this->memberSearch . '%')
                  ->orWhere('users.email', 'ilike', '%' . $this->memberSearch . '%');
            });
        }

        $members = $membersQuery->orderBy('users.name')->paginate(15);

        return view('livewire.super-admin.organization-detail', [
            'members' => $members,
        ]);
    }
}
