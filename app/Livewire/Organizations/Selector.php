<?php

namespace App\Livewire\Organizations;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Selector extends Component
{
    public string $name = '';

    public ?string $primary_color = '#005F02';

    public bool $showInviteModal = false;

    public string $invite_code = '';

    public bool $isSwitching = false;

    public function mount()
    {
        $this->isSwitching = request()->query('mode') === 'switch';
    }

    public function selectOrganization(int $organizationId): void
    {
        $user = Auth::user();

        if (! $user instanceof \App\Models\User) {
            $this->redirectRoute('login');
            return;
        }

        $org = $user->organizations()->whereKey($organizationId)->first();

        if (! $org) {
            $this->addError('name', 'Organization not found or not accessible.');
            return;
        }

        session()->put('current_organization_id', $org->id);

        $this->redirectRoute('dashboard');
    }

    public function createOrganization(): void
    {
        $user = Auth::user();

        if (! $user instanceof \App\Models\User) {
            $this->redirectRoute('login');
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'primary_color' => ['nullable', 'string', 'max:7', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
        ]);

        $validated['primary_color'] = $validated['primary_color']
            ? strtoupper($validated['primary_color'])
            : null;

        $slugBase = Str::slug($validated['name']);
        $slug = $slugBase ?: Str::random(8);

        // Ensure slug uniqueness
        $i = 1;
        while (Organization::query()->where('slug', $slug)->exists()) {
            $i++;
            $slug = $slugBase.'-'.$i;
        }

        $org = Organization::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'primary_color' => $validated['primary_color'] ?: null,
            'created_by' => $user->id,
        ]);

        $org->users()->attach($user->id, ['role' => OrganizationRole::Owner->value]);

        // Seed a few defaults (can be managed later by admin).
        TicketCategory::insert([
            [
                'organization_id' => $org->id,
                'name' => 'Technical',
                'slug' => 'technical',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => $org->id,
                'name' => 'Billing',
                'slug' => 'billing',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => $org->id,
                'name' => 'General',
                'slug' => 'general',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        TicketPriority::insert([
            [
                'organization_id' => $org->id,
                'name' => 'Low',
                'level' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => $org->id,
                'name' => 'Normal',
                'level' => 20,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => $org->id,
                'name' => 'High',
                'level' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => $org->id,
                'name' => 'Urgent',
                'level' => 40,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        session()->put('current_organization_id', $org->id);

        $this->redirectRoute('dashboard');
    }

    public function openInviteModal(): void
    {
        $this->resetErrorBag();
        $this->showInviteModal = true;
    }

    public function closeInviteModal(): void
    {
        $this->resetErrorBag();
        $this->showInviteModal = false;
        $this->invite_code = '';
    }

    public function joinWithInviteCode(): void
    {
        $validated = $this->validate([
            'invite_code' => ['required', 'string', 'max:64'],
        ]);

        // TODO: Implement real invite flow (token lookup + attach user to org).
        $this->addError('invite_code', "Ce système d'invitation n'est pas encore implémenté.");
        $this->showInviteModal = true;
    }

    public function render()
    {
        $user = Auth::user();

        $organizations = $user instanceof \App\Models\User
            ? $user->organizations()->orderBy('name')->get()
            : collect();

        return view('livewire.organizations.selector', [
            'organizations' => $organizations,
        ]);
    }
}
