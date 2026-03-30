@php
    $roleBadge = function (string $role): array {
        return match ($role) {
            'owner' => ['label_key' => 'pages.team.role_owner', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-100', 'icon' => 'solar:crown-bold-duotone'],
            'admin' => ['label_key' => 'pages.team.role_admin', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'icon' => 'solar:shield-check-bold-duotone'],
            'agent' => ['label_key' => 'pages.team.role_agent', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'icon' => 'solar:headphones-round-sound-bold-duotone'],
            'member' => ['label_key' => 'pages.team.role_member', 'bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100', 'icon' => 'solar:user-bold-duotone'],
            default => ['label_key' => null, 'label' => $role, 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-100', 'icon' => 'solar:star-bold-duotone'],
        };
    };
@endphp

<div class="w-full max-w-full min-w-0 mx-auto" x-data="{ activeTab: 'internal' }">

    {{-- ═══ HEADER ═══ --}}
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title">{{ __('pages.team.title') }}</h1>
            <p class="page-subtitle">{{ __('pages.team.subtitle') }}</p>
        </div>
        <div class="page-actions">
            <button
                type="button"
                wire:click="openInviteModal"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-3 sm:py-2.5 text-sm font-bold text-white shadow-lg shadow-[var(--accent-ring)] hover:opacity-90 transition-all transform hover:-translate-y-0.5 touch-target sm:min-h-0 sm:min-w-0 w-full sm:w-auto"
            >
                <iconify-icon icon="solar:user-plus-bold" width="18"></iconify-icon>
                {{ __('pages.team.invite_member') }}
            </button>
        </div>
    </div>

    {{-- ═══ STATS CARDS ═══ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label">{{ __('pages.team.owners') }}</span>
                    <div class="stat-card-value">{{ $stats['owners'] ?? 0 }}</div>
                </div>
                <div class="stat-card-icon bg-purple-50 text-purple-600">
                    <iconify-icon icon="solar:crown-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label">{{ __('pages.team.admins') }}</span>
                    <div class="stat-card-value">{{ $stats['admins'] ?? 0 }}</div>
                </div>
                <div class="stat-card-icon bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:shield-check-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label">{{ __('pages.team.agents') }}</span>
                    <div class="stat-card-value">{{ $stats['agents'] ?? 0 }}</div>
                </div>
                <div class="stat-card-icon bg-amber-50 text-amber-600">
                    <iconify-icon icon="solar:headphones-round-sound-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label">{{ __('pages.team.members') }}</span>
                    <div class="stat-card-value">{{ $stats['members'] ?? 0 }}</div>
                </div>
                <div class="stat-card-icon bg-slate-100 text-slate-600">
                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label">{{ __('pages.team.pending_count') }}</span>
                    <div class="stat-card-value">{{ $pendingInvitations->count() }}</div>
                </div>
                <div class="stat-card-icon bg-orange-50 text-orange-500">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ PENDING INVITATIONS ═══ --}}
    @if($pendingInvitations->isNotEmpty())
        <div class="rounded-xl sm:rounded-2xl overflow-hidden mb-6 sm:mb-8" style="border: 1px solid #fed7aa; background: rgba(255, 247, 237, 0.3);">
            <div class="p-4 flex items-center gap-3" style="border-bottom: 1px solid #ffedd5; background: rgba(255, 237, 213, 0.5);">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
                    <iconify-icon icon="solar:letter-bold-duotone" width="18"></iconify-icon>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('pages.team.invitations_pending') }}</h3>
                    <p class="text-xs text-slate-500">{{ $pendingInvitations->count() }} {{ __('pages.team.invitation_pending') }}</p>
                </div>
            </div>

            <div class="divide-y divide-orange-100">
                @foreach($pendingInvitations as $inv)
                    @php $ib = $roleBadge($inv->role); @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 hover:bg-orange-50/40 transition-colors">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                                <iconify-icon icon="solar:letter-linear" width="20"></iconify-icon>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-900 truncate">{{ $inv->email }}</div>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="pill-badge {{ $ib['bg'] }} {{ $ib['text'] }} {{ $ib['border'] }}" style="font-size: 10px;">
                                        <iconify-icon icon="{{ $ib['icon'] }}" width="12"></iconify-icon>
                                        {{ $ib['label_key'] ? __($ib['label_key']) : ($ib['label'] ?? $inv->role) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ __('pages.team.invited_on', ['date' => $inv->created_at->format('d/m/Y')]) }}
                                    </span>
                                    @if($inv->inviter)
                                        <span class="text-[10px] text-slate-400">
                                            &middot; {{ __('pages.team.invited_by', ['name' => $inv->inviter->name]) }}
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-orange-600 font-medium">
                                        {{ __('pages.team.expires_in', ['days' => (int) now()->diffInDays($inv->expires_at)]) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 sm:ml-4">
                            <button
                                type="button"
                                wire:click="resendInvitation({{ $inv->id }})"
                                class="h-8 px-3 rounded-lg bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition inline-flex items-center gap-1.5"
                                style="border: 1px solid #e2e8f0;"
                                title="{{ __('pages.team.resend') }}"
                            >
                                <iconify-icon icon="solar:refresh-linear" width="14"></iconify-icon>
                                {{ __('pages.team.resend') }}
                            </button>
                            <button
                                type="button"
                                @click="$dispatch('confirm-action', { title: 'Annuler', message: 'Annuler cette invitation ?', confirmLabel: 'Annuler', variant: 'danger', onConfirm: () => $wire.cancelInvitation({{ $inv->id }}) })"
                                class="h-8 px-3 rounded-lg bg-white text-xs font-semibold text-red-600 hover:bg-red-50 transition inline-flex items-center gap-1.5"
                                style="border: 1px solid #fecaca;"
                                title="{{ __('pages.team.cancel_invitation') }}"
                            >
                                <iconify-icon icon="solar:close-circle-linear" width="14"></iconify-icon>
                                {{ __('pages.team.cancel_invitation') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══ TABS NAVIGATION ═══ --}}
    <div class="flex items-center gap-1 p-1 rounded-xl bg-slate-100/80 mb-5 w-fit">
        <button
            type="button"
            @click="activeTab = 'internal'"
            :class="activeTab === 'internal'
                ? 'bg-white text-slate-900 shadow-sm'
                : 'text-slate-500 hover:text-slate-700'"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
        >
            <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="18"></iconify-icon>
            {{ __('pages.team.tab_internal') }}
            <span
                :class="activeTab === 'internal' ? 'bg-[var(--accent)] text-white' : 'bg-slate-200 text-slate-600'"
                class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-[11px] font-bold transition-colors"
            >{{ $stats['total'] ?? 0 }}</span>
        </button>
        <button
            type="button"
            @click="activeTab = 'external'"
            :class="activeTab === 'external'
                ? 'bg-white text-slate-900 shadow-sm'
                : 'text-slate-500 hover:text-slate-700'"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
        >
            <iconify-icon icon="solar:mailbox-bold-duotone" width="18"></iconify-icon>
            {{ __('pages.team.tab_external') }}
            <span
                :class="activeTab === 'external' ? 'bg-[var(--accent)] text-white' : 'bg-slate-200 text-slate-600'"
                class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-[11px] font-bold transition-colors"
            >{{ $stats['external'] ?? 0 }}</span>
        </button>
    </div>

    {{-- ═══ TAB: INTERNAL MEMBERS ═══ --}}
    <div x-show="activeTab === 'internal'" x-cloak>
        <div class="content-card">
            @php
                $roleFilterOptions = array_merge(
                    [['value' => '', 'label' => __('pages.team.all_roles')]],
                    $roles->map(fn ($r) => ['value' => $r->slug, 'label' => $r->name])->all()
                );
                $roleFilterLabel = $role === '' ? __('pages.team.all_roles') : ($roles->firstWhere('slug', $role)?->name ?? $role);
                $perPageOptions = [
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                ];
                $functionRowOptions = array_merge(
                    [['value' => '', 'label' => __('pages.team.no_function')]],
                    $organizationFunctions->map(fn ($fn) => ['value' => (string) $fn->id, 'label' => $fn->name])->all()
                );
                $memberRoleOptions = $roles->map(fn ($r) => ['value' => $r->slug, 'label' => $r->name])->all();
                $inviteRoleDropdownOptions = $roles
                    ->filter(fn ($r) => $r->slug !== 'owner')
                    ->map(fn ($r) => ['value' => $r->slug, 'label' => $r->name])
                    ->values()
                    ->all();
                $inviteRoleLabel = $roles->firstWhere('slug', $inviteRole)?->name ?? __('pages.team.role');
            @endphp
            {{-- Toolbar — listes HTML (pas de panneau OS) pour rôle / taille de page --}}
            <div class="filter-bar">
                <div class="filter-bar-row">
                    <div class="flex-1 relative">
                        <iconify-icon icon="solar:magnifer-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300" width="18"></iconify-icon>
                        <input
                            type="text"
                            class="filter-search"
                            placeholder="{{ __('pages.team.search_placeholder') }}"
                            wire:model.live="search"
                        />
                    </div>
                    <div class="filter-controls">
                        <div class="w-44 min-w-[10rem]">
                            <x-dropdown-select
                                :options="$roleFilterOptions"
                                :label="$roleFilterLabel"
                                :selected-value="$role"
                                model-name="role"
                            />
                        </div>
                        <div class="w-24 min-w-[5.5rem]">
                            <x-dropdown-select
                                :options="$perPageOptions"
                                :label="(string) $perPage"
                                :selected-value="$perPage"
                                model-name="perPage"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('pages.team.member') }}</th>
                            <th>{{ __('pages.team.role') }}</th>
                            <th>{{ __('pages.team.business_function') }}</th>
                            <th class="text-right">{{ __('pages.team.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($memberships as $m)
                            @php $b = $roleBadge($m->role); @endphp
                            <tr class="group">
                                <td>
                                    <div class="flex items-center gap-4">
                                        <x-avatar :name="$m->user?->name ?? 'U'" size="h-10 w-10" class="ring-2 ring-white shadow-sm" />
                                        <div>
                                            <div class="text-sm font-bold text-slate-900">{{ $m->user?->name ?? __('pages.team.unknown_user') }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">{{ $m->user?->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="pill-badge {{ $b['bg'] }} {{ $b['text'] }} {{ $b['border'] }}">
                                        <iconify-icon icon="{{ $b['icon'] }}" width="14"></iconify-icon>
                                        {{ $b['label_key'] ? __($b['label_key']) : ($b['label'] ?? $m->role) }}
                                    </span>
                                </td>
                                <td class="min-w-[140px]">
                                    <x-dropdown-select
                                        :options="$functionRowOptions"
                                        :label="$organizationFunctions->firstWhere('id', $m->organization_function_id)?->name ?? __('pages.team.no_function')"
                                        :selected-value="$m->organization_function_id !== null ? (string) $m->organization_function_id : ''"
                                        wire-method="updateFunction"
                                        :wire-target-id="(int) $m->id"
                                        :instance-key="'fn-' . $m->id"
                                        compact
                                    />
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="min-w-[8.5rem]">
                                            <x-dropdown-select
                                                :options="$memberRoleOptions"
                                                :label="$roles->firstWhere('slug', $m->role)?->name ?? $m->role"
                                                :selected-value="$m->role"
                                                wire-method="updateRole"
                                                :wire-target-id="(int) $m->id"
                                                :instance-key="'role-' . $m->id"
                                                compact
                                            />
                                        </div>

                                        <button
                                            type="button"
                                            class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                            @click="$dispatch('confirm-action', { title: 'Retirer', message: 'Retirer ce membre de l\u0027\u00e9quipe ?', confirmLabel: 'Retirer', variant: 'danger', onConfirm: () => $wire.removeMember({{ (int) $m->id }}) })"
                                            title="{{ __('pages.team.remove_from_team') }}"
                                        >
                                            <iconify-icon icon="solar:trash-bin-trash-bold" width="16"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <iconify-icon icon="solar:users-group-rounded-linear" width="28" class="text-slate-300"></iconify-icon>
                                        </div>
                                        <p class="empty-state-title">{{ __('pages.team.no_members_found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 bg-slate-50/30" style="border-top: 1px solid #f1f5f9;">
                {{ $memberships->links() }}
            </div>
        </div>
    </div>

    {{-- ═══ TAB: EXTERNAL CONTACTS ═══ --}}
    <div x-show="activeTab === 'external'" x-cloak>
        <div class="content-card">
            {{-- Toolbar --}}
            <div class="filter-bar">
                <div class="filter-bar-row">
                    <div class="flex-1 relative">
                        <iconify-icon icon="solar:magnifer-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300" width="18"></iconify-icon>
                        <input
                            type="text"
                            class="filter-search"
                            placeholder="{{ __('pages.team.search_external_placeholder') }}"
                            wire:model.live="searchExternal"
                        />
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('pages.team.member') }}</th>
                            <th>{{ __('pages.team.external_col_source') }}</th>
                            <th>{{ __('pages.team.external_col_first_contact') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($externalContacts as $ec)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <x-avatar :name="$ec->user?->name ?? 'U'" size="h-10 w-10" class="ring-2 ring-white shadow-sm" />
                                            <div class="absolute -bottom-0.5 -right-0.5 h-4 w-4 rounded-full bg-slate-100 flex items-center justify-center ring-2 ring-white">
                                                <iconify-icon icon="solar:letter-bold" width="10" class="text-slate-400"></iconify-icon>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900">{{ $ec->user?->name ?? __('pages.team.unknown_user') }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">{{ $ec->user?->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="pill-badge bg-sky-50 text-sky-600 border-sky-100">
                                        <iconify-icon icon="solar:letter-bold-duotone" width="14"></iconify-icon>
                                        {{ __('pages.team.external_source') }}
                                    </span>
                                </td>
                                <td class="text-sm text-slate-500">
                                    {{ $ec->created_at?->format('d/m/Y') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <iconify-icon icon="solar:mailbox-linear" width="28" class="text-slate-300"></iconify-icon>
                                        </div>
                                        <p class="empty-state-title">{{ __('pages.team.no_external_contacts') }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ __('pages.team.no_external_contacts_hint') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══ INVITE MODAL ═══ --}}
    <div x-data="{ open: $wire.$entangle('showInviteModal') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4" style="display:none;">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="$wire.closeInviteModal()"
        ></div>

        <div
            x-show="open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden"
            style="border: 1px solid #f1f5f9;"
            @click.stop
        >
            <div class="px-5 py-4 flex items-center justify-between" style="border-bottom: 1px solid #f1f5f9;">
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-slate-900">{{ __('pages.team.invite_member') }}</div>
                    <div class="text-xs text-slate-500">{{ __('pages.team.invite_modal_subtitle') }}</div>
                </div>
                <button type="button" wire:click="closeInviteModal" class="h-9 w-9 rounded-xl bg-white text-slate-500 hover:bg-slate-50 transition flex items-center justify-center" style="border: 1px solid #e2e8f0;" aria-label="{{ __('pages.team.close') }}">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            <form wire:submit.prevent="sendInvite" class="p-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-700">{{ __('pages.team.email') }}</label>
                    <input
                        type="email"
                        wire:model.live.debounce.200ms="inviteEmail"
                        class="block w-full rounded-xl bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                        style="border: 1px solid #e2e8f0;"
                        placeholder="{{ __('pages.team.email_placeholder') }}"
                        required
                    >
                    <x-input-error :messages="$errors->get('inviteEmail')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-700">{{ __('pages.team.role') }}</label>
                    <x-dropdown-select
                        :options="$inviteRoleDropdownOptions"
                        :label="$inviteRoleLabel"
                        :selected-value="$inviteRole"
                        model-name="inviteRole"
                        instance-key="invite-role"
                    />
                    <x-input-error :messages="$errors->get('inviteRole')" />
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeInviteModal" class="h-10 px-4 rounded-xl bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition" style="border: 1px solid #e2e8f0;">
                        {{ __('pages.team.cancel') }}
                    </button>
                    <button type="submit" class="h-10 px-4 rounded-xl bg-[var(--accent)] text-white text-sm font-extrabold shadow-sm hover:opacity-90 transition inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="sendInvite">{{ __('pages.team.send') }}</span>
                        <span wire:loading wire:target="sendInvite">{{ __('pages.team.sending') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
