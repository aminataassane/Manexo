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

<div class="mx-auto w-full min-w-0 max-w-7xl 2xl:max-w-[90rem] min-[1920px]:max-w-[110rem] py-4 sm:py-6 lg:py-8 px-3 sm:px-6 lg:px-8">
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight sm:text-2xl">{{ __('pages.team.title') }}</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">{{ __('pages.team.subtitle') }}</p>
        </div>
        <button
            type="button"
            wire:click="openInviteModal"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-3 sm:py-2.5 text-sm font-bold text-white shadow-lg shadow-[var(--accent-ring)] hover:opacity-90 transition-all transform hover:-translate-y-0.5 touch-target sm:min-h-0 sm:min-w-0 w-full sm:w-auto"
        >
            <iconify-icon icon="solar:user-plus-bold" width="18"></iconify-icon>
            {{ __('pages.team.invite_member') }}
        </button>
    </div>

    <!-- STATS CARDS (2 cols mobile, 5 lg) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <!-- Owners -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('pages.team.owners') }}</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['owners'] ?? 0 }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600 group-hover:scale-110 transition-transform">
                    <iconify-icon icon="solar:crown-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Admins -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('pages.team.admins') }}</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['admins'] ?? 0 }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform">
                    <iconify-icon icon="solar:shield-check-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Agents -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('pages.team.agents') }}</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['agents'] ?? 0 }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform">
                    <iconify-icon icon="solar:headphones-round-sound-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Members -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('pages.team.members') }}</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['members'] ?? 0 }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:scale-110 transition-transform">
                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Pending Invitations -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('pages.team.pending_count') }}</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $pendingInvitations->count() }}</h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-orange-500 group-hover:scale-110 transition-transform">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="24"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- PENDING INVITATIONS -->
    @if($pendingInvitations->isNotEmpty())
        <div class="rounded-2xl border border-orange-200 bg-orange-50/30 shadow-sm overflow-hidden mb-6 sm:mb-8">
            <div class="p-4 border-b border-orange-100 bg-orange-50/50 flex items-center gap-3">
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
                    @php($ib = $roleBadge($inv->role))
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 hover:bg-orange-50/40 transition-colors">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                                <iconify-icon icon="solar:letter-linear" width="20"></iconify-icon>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-900 truncate">{{ $inv->email }}</div>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $ib['bg'] }} {{ $ib['text'] }} {{ $ib['border'] }}">
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
                                class="h-8 px-3 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition inline-flex items-center gap-1.5"
                                title="{{ __('pages.team.resend') }}"
                            >
                                <iconify-icon icon="solar:refresh-linear" width="14"></iconify-icon>
                                {{ __('pages.team.resend') }}
                            </button>
                            <button
                                type="button"
                                @click="$dispatch('confirm-action', { title: 'Annuler', message: 'Annuler cette invitation ?', confirmLabel: 'Annuler', variant: 'danger', onConfirm: () => $wire.cancelInvitation({{ $inv->id }}) })"
                                class="h-8 px-3 rounded-lg border border-red-200 bg-white text-xs font-semibold text-red-600 hover:bg-red-50 transition inline-flex items-center gap-1.5"
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

    <!-- MEMBERS LIST -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <!-- Toolbar -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex-1 relative">
                <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="18"></iconify-icon>
                <input
                    type="text"
                    class="w-full h-10 pl-10 pr-4 rounded-xl border-slate-200 bg-white text-sm text-slate-900 placeholder:text-slate-400 focus:border-[var(--accent)] focus:ring-[var(--accent)] transition-shadow shadow-sm"
                    placeholder="{{ __('pages.team.search_placeholder') }}"
                    wire:model.live="search"
                />
            </div>

            <div class="flex gap-3">
                <div class="w-40">
                    <x-select-input wire:model.live="role">
                        <option value="">{{ __('pages.team.all_roles') }}</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->slug }}">{{ $r->name }}</option>
                        @endforeach
                    </x-select-input>
                </div>

                <div class="w-24">
                    <x-select-input wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </x-select-input>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">{{ __('pages.team.member') }}</th>
                        <th class="px-6 py-4">{{ __('pages.team.role') }}</th>
                        <th class="px-6 py-4">{{ __('pages.team.business_function') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('pages.team.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($memberships as $m)
                        @php($b = $roleBadge($m->role))
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <x-avatar :name="$m->user?->name ?? 'U'" size="h-10 w-10" class="ring-2 ring-white shadow-sm" />
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ $m->user?->name ?? __('pages.team.unknown_user') }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $m->user?->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $b['bg'] }} {{ $b['text'] }} {{ $b['border'] }}">
                                    <iconify-icon icon="{{ $b['icon'] }}" width="14"></iconify-icon>
                                    {{ $b['label_key'] ? __($b['label_key']) : ($b['label'] ?? $m->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <select
                                    class="h-8 min-w-[140px] rounded-lg border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] pl-2 pr-8"
                                    wire:change="updateFunction({{ (int) $m->id }}, $event.target.value)"
                                >
                                    <option value="">{{ __('pages.team.no_function') }}</option>
                                    @foreach ($organizationFunctions as $fn)
                                        <option value="{{ $fn->id }}" @selected($m->organization_function_id === $fn->id)>{{ $fn->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="relative">
                                        <select
                                            class="h-8 rounded-lg border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] pl-2 pr-8"
                                            wire:change="updateRole({{ (int) $m->id }}, $event.target.value)"
                                        >
                                            @foreach ($roles as $r)
                                                <option value="{{ $r->slug }}" @selected($m->role === $r->slug)>{{ $r->name }}</option>
                                            @endforeach
                                        </select>
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
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                                        <iconify-icon icon="solar:users-group-rounded-linear" width="24" class="text-slate-400"></iconify-icon>
                                    </div>
                                    <p class="text-sm font-medium">{{ __('pages.team.no_members_found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $memberships->links() }}
        </div>
    </div>

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
            class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white shadow-2xl overflow-hidden"
            @click.stop
        >
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-slate-900">{{ __('pages.team.invite_member') }}</div>
                    <div class="text-xs text-slate-500">{{ __('pages.team.invite_modal_subtitle') }}</div>
                </div>
                <button type="button" wire:click="closeInviteModal" class="h-9 w-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 transition flex items-center justify-center" aria-label="{{ __('pages.team.close') }}">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            <form wire:submit.prevent="sendInvite" class="p-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-700">{{ __('pages.team.email') }}</label>
                    <input
                        type="email"
                        wire:model.live.debounce.200ms="inviteEmail"
                        class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                        placeholder="{{ __('pages.team.email_placeholder') }}"
                        required
                    >
                    <x-input-error :messages="$errors->get('inviteEmail')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-700">{{ __('pages.team.role') }}</label>
                    <x-select-input wire:model.live="inviteRole">
                        @foreach ($roles as $r)
                            @if ($r->slug !== 'owner')
                                <option value="{{ $r->slug }}">{{ $r->name }}</option>
                            @endif
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('inviteRole')" />
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeInviteModal" class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
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
