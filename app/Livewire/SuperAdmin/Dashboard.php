<?php

namespace App\Livewire\SuperAdmin;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.super-admin', ['title' => 'super_admin.dashboard.title'])]
class Dashboard extends Component
{
    #[Computed]
    public function stats(): array
    {
        $orgCounts = DB::table('organizations')
            ->select([
                DB::raw("COUNT(*) as total"),
                DB::raw("COUNT(*) FILTER (WHERE status = 'active' OR status IS NULL) as active"),
                DB::raw("COUNT(*) FILTER (WHERE status = 'suspended') as suspended"),
                DB::raw("COUNT(*) FILTER (WHERE status = 'disabled') as disabled"),
            ])
            ->first();

        $totalUsers = DB::table('users')->count();

        return [
            'total_orgs' => (int) $orgCounts->total,
            'active_orgs' => (int) $orgCounts->active,
            'suspended_orgs' => (int) $orgCounts->suspended,
            'disabled_orgs' => (int) $orgCounts->disabled,
            'total_users' => $totalUsers,
        ];
    }

    public function render()
    {
        return view('livewire.super-admin.dashboard');
    }
}
