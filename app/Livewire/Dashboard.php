<?php

namespace App\Livewire;

use App\Enums\Permission;
use App\Livewire\Dashboard\Concerns\WithOrganization;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manexo-app', ['title' => 'pages.dashboard.title'])]
class Dashboard extends Component
{
    use WithOrganization;

    #[Computed]
    public function isAdminView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->hasAnyPermission([
            Permission::TicketsViewAll,
            Permission::ReportsView,
            Permission::TeamInvite,
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
