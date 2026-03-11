<?php

namespace App\Livewire\SuperAdmin;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.super-admin', ['title' => 'super_admin.profile.title'])]
class Profile extends Component
{
    #[Computed]
    public function sessions(): \Illuminate\Support\Collection
    {
        return DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderByDesc('last_activity')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function currentSessionId(): string
    {
        return session()->getId();
    }

    #[Computed]
    public function organizations(): \Illuminate\Support\Collection
    {
        return Auth::user()
            ?->organizations()
            ->withPivot(['role'])
            ->orderBy('name')
            ->get() ?? collect();
    }

    public function render()
    {
        return view('livewire.super-admin.profile');
    }
}
