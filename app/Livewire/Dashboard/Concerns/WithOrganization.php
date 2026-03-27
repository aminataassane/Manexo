<?php

namespace App\Livewire\Dashboard\Concerns;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

trait WithOrganization
{
    #[Computed]
    public function organization(): ?\App\Models\Organization
    {
        $org = request()->attributes->get('currentOrganization');
        if ($org !== null) {
            return $org;
        }
        $orgId = session('current_organization_id');
        if (! $orgId) {
            return null;
        }
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user) {
            return \App\Models\Organization::find($orgId);
        }

        return $user->organizations()->whereKey($orgId)->first();
    }

    #[Computed]
    public function orgId(): ?int
    {
        return $this->organization?->id;
    }
}
